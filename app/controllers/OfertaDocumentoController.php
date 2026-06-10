<?php
// app/controllers/OfertaDocumentoController.php - VERSIÓN COMPLETA CORREGIDA
namespace App\Controllers;

use App\Models\OfertaDocumento;
use App\Models\Oferta;

class OfertaDocumentoController 
{
    /**
     * GET /api/ofertas/{id}/documentos
     * Listar documentos de una oferta
     */
    public function index($oferta_id)
    {
        try {
            // Verificar que la oferta existe
            Oferta::findOrFail($oferta_id);
            
            // Obtener documentos
            $documentos = OfertaDocumento::where('licitacion_id', $oferta_id)
                ->orderBy('creado_en', 'desc')
                ->get();
            
            // Formatear respuesta
            $data = $documentos->map(function ($documento) {
                return [
                    'id' => $documento->id,
                    'titulo' => $documento->titulo,
                    'descripcion' => $documento->descripcion,
                    'archivo' => $documento->archivo,
                    'archivo_url' => $this->getArchivoUrl($documento->archivo),
                    'tipo_archivo' => pathinfo($documento->archivo, PATHINFO_EXTENSION),
                    'creado_en' => $documento->creado_en,
                    'icono' => $this->getIcono($documento->archivo)
                ];
            });
            
            echo json_encode([
                'success' => true,
                'data' => $data,
                'count' => $documentos->count()
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener documentos',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * POST /api/ofertas/{id}/documentos
     * Crear nuevo documento
     */
    public function store($oferta_id)
    {
        try {
            // Verificar que la oferta existe
            $oferta = Oferta::findOrFail($oferta_id);
            
            // Validar que se envió archivo
            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Debe seleccionar un archivo válido');
            }
            
            $archivo = $_FILES['archivo'];
            
            // Validaciones según PDF (PDF o ZIP, máximo 10MB)
            $errors = $this->validarDocumento($archivo, $_POST);
            
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'errors' => $errors,
                    'message' => 'Validación de documento fallida'
                ]);
                return;
            }
            
            // Crear directorio de uploads si no existe
            $uploadDir = __DIR__ . '/../../uploads/documentos/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generar nombre único para el archivo
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $nombreArchivo = 'doc_' . $oferta_id . '_' . time() . '_' . uniqid() . '.' . $extension;
            $rutaArchivo = $uploadDir . $nombreArchivo;
            
            // Mover archivo al servidor
            if (!move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
                throw new \Exception('Error al guardar el archivo en el servidor');
            }
            
            // Guardar en base de datos
            $documento = OfertaDocumento::create([
                'licitacion_id' => $oferta_id,
                'titulo' => substr($_POST['titulo'] ?? '', 0, 100),
                'descripcion' => substr($_POST['descripcion'] ?? '', 0, 200),
                'archivo' => 'documentos/' . $nombreArchivo, // Ruta relativa
                'creado_en' => date('Y-m-d H:i:s')
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Documento cargado exitosamente',
                'data' => [
                    'id' => $documento->id,
                    'titulo' => $documento->titulo,
                    'descripcion' => $documento->descripcion,
                    'archivo' => $documento->archivo,
                    'archivo_url' => $this->getArchivoUrl($documento->archivo),
                    'tipo_archivo' => $extension,
                    'creado_en' => $documento->creado_en,
                    'icono' => $this->getIcono($documento->archivo)
                ]
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al cargar documento',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * DELETE /api/documentos/{id}
     * Eliminar documento
     */
    public function destroy($id)
    {
        try {
            $documento = OfertaDocumento::findOrFail($id);
            
            // Eliminar archivo físico
            $rutaArchivo = __DIR__ . '/../../uploads/' . $documento->archivo;
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
            
            // Eliminar de la base de datos
            $documento->delete();
            
            echo json_encode([
                'success' => true,
                'message' => 'Documento eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al eliminar documento',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * GET /api/documentos/{id}/download
     * Descargar documento
     */
    public function download($id)
    {
        try {
            $documento = OfertaDocumento::findOrFail($id);
            $rutaArchivo = __DIR__ . '/../../uploads/' . $documento->archivo;
            
            if (!file_exists($rutaArchivo)) {
                throw new \Exception('Archivo no encontrado en el servidor');
            }
            
            // Determinar tipo MIME
            $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
            $mimeTypes = [
                'pdf' => 'application/pdf',
                'zip' => 'application/zip',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            
            $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
            
            // Configurar headers para descarga
            header('Content-Type: ' . $contentType);
            header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
            header('Content-Length: ' . filesize($rutaArchivo));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            // Limpiar buffer y enviar archivo
            while (ob_get_level()) ob_end_clean();
            readfile($rutaArchivo);
            exit;
            
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Documento no encontrado',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * GET /api/documentos/{id}/view
     * Ver documento (en navegador para PDF)
     */
    public function view($id)
    {
        try {
            $documento = OfertaDocumento::findOrFail($id);
            $rutaArchivo = __DIR__ . '/../../uploads/' . $documento->archivo;
            
            if (!file_exists($rutaArchivo)) {
                throw new \Exception('Archivo no encontrado');
            }
            
            $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
            
            if ($extension === 'pdf') {
                // Mostrar PDF en navegador
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . basename($rutaArchivo) . '"');
                header('Content-Length: ' . filesize($rutaArchivo));
                
                while (ob_get_level()) ob_end_clean();
                readfile($rutaArchivo);
                exit;
            } else {
                // Para ZIP u otros, redirigir a download
                $this->download($id);
            }
            
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Error al visualizar documento',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    // ==================== MÉTODOS PRIVADOS ====================
    
    /**
     * Validar documento según requerimientos del PDF
     */
    private function validarDocumento($archivo, $data)
    {
        $errors = [];
        
        // 1. Validar título (requerido, máximo 100 caracteres según PDF)
        if (empty($data['titulo'] ?? '')) {
            $errors['titulo'] = 'El título es requerido';
        } elseif (strlen($data['titulo']) > 100) {
            $errors['titulo'] = 'Máximo 100 caracteres';
        }
        
        // 2. Validar descripción (máximo 200 caracteres según PDF)
        if (!empty($data['descripcion']) && strlen($data['descripcion']) > 200) {
            $errors['descripcion'] = 'Máximo 200 caracteres';
        }
        
        // 3. Validar archivo
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
                UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco',
                UPLOAD_ERR_EXTENSION => 'Una extensión PHP detuvo la carga del archivo'
            ];
            
            $errors['archivo'] = $errorMessages[$archivo['error']] ?? 'Error desconocido en la carga';
        } else {
            // 4. Validar tipo de archivo (PDF o ZIP según PDF)
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'zip'];
            
            if (!in_array($extension, $allowedExtensions)) {
                $errors['archivo'] = 'Solo se permiten archivos PDF o ZIP. Tipo detectado: .' . $extension;
            }
            
            // 5. Validar tamaño (máximo 10MB según práctica común)
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($archivo['size'] > $maxSize) {
                $errors['archivo'] = 'El archivo no debe superar los 10MB. Tamaño actual: ' . 
                                    round($archivo['size'] / 1024 / 1024, 2) . 'MB';
            }
            
            // 6. Validar tipo MIME real (seguridad adicional)
            $allowedMimeTypes = [
                'application/pdf',
                'application/zip',
                'application/x-zip-compressed',
                'application/x-zip',
                'application/octet-stream' // Para algunos ZIP
            ];
            
            // Usar finfo si está disponible
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $archivo['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedMimeTypes)) {
                    $errors['archivo'] = 'Tipo de archivo no permitido. Tipo MIME detectado: ' . $mimeType;
                }
            }
            
            // 7. Validar nombre del archivo (seguridad)
            if (preg_match('/[^\w\.\-]/', $archivo['name'])) {
                $errors['archivo'] = 'El nombre del archivo contiene caracteres no permitidos';
            }
        }
        
        return $errors;
    }
    
    /**
     * Generar URL completa para el archivo
     */
    private function getArchivoUrl($rutaRelativa)
    {
        if (!$rutaRelativa) return null;
        
        // Determinar la URL base automáticamente
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        
        return $protocol . '://' . $host . $basePath . '/uploads/' . $rutaRelativa;
    }
    
    /**
     * Obtener icono según tipo de archivo
     */
    private function getIcono($rutaArchivo)
    {
        if (!$rutaArchivo) return 'bi-file';
        
        $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
        
        $iconos = [
            'pdf' => 'bi-file-earmark-pdf text-danger',
            'zip' => 'bi-file-earmark-zip text-warning',
            'doc' => 'bi-file-earmark-word text-primary',
            'docx' => 'bi-file-earmark-word text-primary',
            'xls' => 'bi-file-earmark-excel text-success',
            'xlsx' => 'bi-file-earmark-excel text-success'
        ];
        
        return $iconos[$extension] ?? 'bi-file';
    }
    
    /**
     * GET /api/ofertas/{id}/documentos/count
     * Contar documentos de una oferta (útil para validación en edición)
     */
    public function count($oferta_id)
    {
        try {
            $count = OfertaDocumento::where('licitacion_id', $oferta_id)->count();
            
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al contar documentos',
                'message' => $e->getMessage()
            ]);
        }
    }
}