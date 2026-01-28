<?php
namespace App\Controllers;

use App\Models\Actividad;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ActividadController 
{
    /**
     * Vista para cargar actividades desde Excel
     */
    public function mostrarFormulario()
    {
         try {
        // Obtener total de actividades
        $totalActividades = Actividad::count();
        
        // Pasar a la vista
        require 'app/views/actividades/cargar.view.php';
        
    } catch (\Exception $e) {
        // Si hay error (tabla no existe), usar 0
        $totalActividades = 0;
        require 'app/views/actividades/cargar.view.php';
    }
    }
    
    /**
     * Procesar la importación desde Excel
     */
    public function importar()
    {
        try {
            // Validar que sea POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Método no permitido');
            }
            
            // Validar archivo
            if (!isset($_FILES['archivo_excel']) || $_FILES['archivo_excel']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('No se ha subido ningún archivo Excel');
            }
            
            $archivoTemp = $_FILES['archivo_excel']['tmp_name'];
            $nombreOriginal = $_FILES['archivo_excel']['name'];
            
            // Validar extensión
            $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
            if (!in_array($extension, ['xlsx', 'xls'])) {
                throw new \Exception('Solo se permiten archivos Excel (.xlsx, .xls)');
            }
            
            // Validar tamaño (máx 10MB)
            if ($_FILES['archivo_excel']['size'] > 10485760) {
                throw new \Exception('El archivo no debe superar los 10MB');
            }
            
            // Cargar el Excel
            $spreadsheet = IOFactory::load($archivoTemp);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Obtener datos como array
            $data = $worksheet->toArray();
            
            // Validar que tenga datos
            if (count($data) < 2) {
                throw new \Exception('El archivo Excel está vacío o no tiene el formato correcto');
            }
            
            // Contadores
            $insertados = 0;
            $actualizados = 0;
            $errores = [];
            
            // Procesar cada fila (empezar desde 1 para saltar headers)
            for ($i = 1; $i < count($data); $i++) {
                $fila = $data[$i];
                
                // Validar fila mínima
                if (empty($fila[0]) || empty($fila[1])) {
                    continue; // Saltar filas vacías
                }
                
                try {
                    $actividadData = [
                        'codigo_segmento' => (int) $fila[0],
                        'segmento' => $this->limpiarTexto($fila[1] ?? ''),
                        'codigo_familia' => (int) $fila[2],
                        'familia' => $this->limpiarTexto($fila[3] ?? ''),
                        'codigo_clase' => (int) $fila[4],
                        'clase' => $this->limpiarTexto($fila[5] ?? ''),
                        'codigo_producto' => (int) $fila[6],
                        'producto' => $this->limpiarTexto($fila[7] ?? ''),
                    ];
                    
                    // Buscar actividad existente por clave única
                    $existente = Actividad::where('codigo_segmento', $actividadData['codigo_segmento'])
                        ->where('codigo_familia', $actividadData['codigo_familia'])
                        ->where('codigo_clase', $actividadData['codigo_clase'])
                        ->where('codigo_producto', $actividadData['codigo_producto'])
                        ->first();
                    
                    if ($existente) {
                        // Actualizar
                        $existente->update($actividadData);
                        $actualizados++;
                    } else {
                        // Crear nueva
                        Actividad::create($actividadData);
                        $insertados++;
                    }
                    
                } catch (\Exception $e) {
                    $errores[] = "Fila " . ($i + 1) . ": " . $e->getMessage();
                }
            }
            
            // Preparar respuesta
            $resultado = [
                'success' => true,
                'message' => "Importación completada: $insertados nuevos, $actualizados actualizados.",
                'data' => [
                    'insertados' => $insertados,
                    'actualizados' => $actualizados,
                    'total' => $insertados + $actualizados,
                    'errores' => count($errores)
                ]
            ];
            
            if (!empty($errores)) {
                $resultado['errores_detalle'] = $errores;
                $resultado['warning'] = 'Hubo ' . count($errores) . ' errores durante la importación';
            }
            
            // Guardar en sesión para mostrar en vista
            session_start();
            $_SESSION['import_result'] = $resultado;
            
            // Redirigir a la vista
            header('Location: ' . BASE_URL . '/actividades/cargar');
            exit;
            
        } catch (\Exception $e) {
            // Error general
            session_start();
            $_SESSION['error'] = 'Error al importar actividades: ' . $e->getMessage();
            
            header('Location: ' . BASE_URL . '/actividades/cargar');
            exit;
        }
    }
    
    /**
     * Para select dinámico en frontend (API JSON)
     */
    public function index()
    {
        try {
            // Para select dinámico en frontend
            $actividades = Actividad::select('id', 'producto', 'clase', 'familia', 'segmento',
                                            'codigo_segmento', 'codigo_familia', 'codigo_clase', 'codigo_producto')
                                    ->orderBy('producto')
                                    ->get();
            
            echo json_encode([
                'success' => true,
                'data' => $actividades
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener actividades'
            ]);
        }
    }
    
    /**
     * Limpiar texto para BD
     */
    private function limpiarTexto($texto)
    {
        $texto = trim($texto);
        $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
        return $texto;
    }
    
    /**
     * Vista para ver actividades cargadas
     */
    public function listar()
    {
        $actividades = Actividad::orderBy('codigo_segmento')
                               ->orderBy('codigo_familia')
                               ->orderBy('codigo_clase')
                               ->orderBy('codigo_producto')
                               ->paginate(50);
        
        require 'app/views/actividades/listar.view.php';
    }
}