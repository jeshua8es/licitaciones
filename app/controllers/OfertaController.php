<?php

namespace App\Controllers;

use App\Models\Oferta;
use App\Models\Actividad;
use App\Models\Documento;

class OfertaController extends Controller
{
    public function index()
    {
        $ofertas = Oferta::orderBy('creado_en', 'desc')->get();

        $this->view('ofertas/index', [
            'ofertas' => $ofertas,
            'BASE_URL' => BASE_URL
        ]);
    }

    public function crear()
    {
        $actividades = Actividad::all();

        $this->view('ofertas/crear', [
            'actividades' => $actividades,
            'BASE_URL' => BASE_URL
        ]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // 1. Validación básica
                if (empty($_POST['objeto']) || strlen(trim($_POST['objeto'])) > 150) {
                    throw new \Exception('Objeto es requerido (máximo 150 caracteres)');
                }

                if (empty($_POST['descripcion']) || strlen(trim($_POST['descripcion'])) > 400) {
                    throw new \Exception('Descripción es requerida (máximo 400 caracteres)');
                }

                if (empty($_POST['presupuesto']) || floatval($_POST['presupuesto']) <= 0) {
                    throw new \Exception('Presupuesto debe ser mayor a 0');
                }

                // Validar fechas
                $inicio = strtotime($_POST['fecha_inicio'] . ' ' . $_POST['hora_inicio']);
                $cierre = strtotime($_POST['fecha_cierre'] . ' ' . $_POST['hora_cierre']);

                if ($cierre <= $inicio) {
                    throw new \Exception('Fecha/hora de cierre debe ser posterior al inicio');
                }

                // 2. Generar consecutivo CORRECTO
                $ultima = Oferta::orderBy('id', 'desc')->first();

                if ($ultima && preg_match('/O-(\d{4})-\d{2}/', $ultima->consecutivo, $matches)) {
                    $numero = intval($matches[1]) + 1;
                } else {
                    $numero = 1;
                }

                $consecutivo = 'O-' . str_pad($numero, 4, '0', STR_PAD_LEFT) . '-' . date('y');

                // 3. Crear oferta
                $oferta = new Oferta();
                $oferta->consecutivo = $consecutivo;
                $oferta->objeto = trim($_POST['objeto']);
                $oferta->descripcion = trim($_POST['descripcion']);
                $oferta->moneda = $_POST['moneda'];
                $oferta->presupuesto = floatval($_POST['presupuesto']);
                $oferta->actividad_id = !empty($_POST['actividad_id']) ? intval($_POST['actividad_id']) : null;
                $oferta->fecha_inicio = $_POST['fecha_inicio'];
                $oferta->hora_inicio = $_POST['hora_inicio'];
                $oferta->fecha_cierre = $_POST['fecha_cierre'];
                $oferta->hora_cierre = $_POST['hora_cierre'];
                $oferta->estado = 'pendiente';
                $oferta->creado_en = date('Y-m-d H:i:s');

                // 4. Guardar
                if ($oferta->save()) {
                    $_SESSION['success'] = "¡Oferta creada exitosamente! Consecutivo: $consecutivo";
                    $this->redirect(BASE_URL . '/oferta');
                } else {
                    throw new \Exception('Error al guardar en la base de datos');
                }
            } catch (\Exception $e) {
                // Si hay error, recargar actividades y mostrar formulario
                $actividades = Actividad::all();

                $this->view('ofertas/crear', [
                    'error' => $e->getMessage(),
                    'datos' => $_POST,
                    'actividades' => $actividades,
                    'BASE_URL' => BASE_URL
                ]);
            }
        } else {
            // Si no es POST, redirigir al formulario
            $this->redirect(BASE_URL . '/oferta/crear');
        }
    }

    public function show($id)
    {
        $oferta = Oferta::find($id);

        if (!$oferta) {
            $this->view('errors/404', ['message' => 'Oferta no encontrada']);
            return;
        }

        $this->view('ofertas/ver', [
            'oferta' => $oferta,
            'BASE_URL' => BASE_URL
        ]);
    }
    // Agrega esto JUSTO DESPUÉS del método show()
    public function ver($id)
    {
        $oferta = Oferta::find($id);

        if (!$oferta) {
            echo '<h1>404 - Oferta no encontrada</h1>';
            exit;
        }

        // Usa la vista CORRECTA con Bootstrap
        $this->view('ofertas/ver', [
            'oferta' => $oferta->toArray(), // Convertir a array para la vista
            'BASE_URL' => BASE_URL
        ]);
    }

    public function editar($id)
    {
        $oferta = Oferta::find($id);
        $actividades = Actividad::all();
        $documentos = Documento::where('licitacion_id', $id)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        if (!$oferta) {
            $this->view('errors/404', ['message' => 'Oferta no encontrada']);
            return;
        }

        $this->view('ofertas/editar', [
            'oferta' => $oferta,
            'actividades' => $actividades,
            'documentos' => $documentos,
            'BASE_URL' => BASE_URL
        ]);
    }

    public function eliminar($id)
    {
        $oferta = Oferta::find($id);

        if ($oferta) {
            $oferta->delete();
            $_SESSION['success'] = "Oferta eliminada";
        }

        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
    public function actualizar($id)
    {
        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        $isAjax = (strtolower($requestedWith) === 'xmlhttprequest') || (strpos($acceptHeader, 'application/json') !== false);

        try {
            // 1. Validar que exista al menos 1 documento
            $documentosExistentes = Documento::where('licitacion_id', $id)->count();

            // Contar nuevos archivos
            $nuevosArchivos = 0;
            if (isset($_FILES['documentos']) && is_array($_FILES['documentos']['name'])) {
                foreach ($_FILES['documentos']['name'] as $index => $nombre) {
                    if (!empty($nombre) && $_FILES['documentos']['error'][$index] === UPLOAD_ERR_OK) {
                        $nuevosArchivos++;
                    }
                }
            }

            // REGLA PDF: En edición debe haber al menos 1 documento
            if ($documentosExistentes === 0 && $nuevosArchivos === 0) {
                throw new \Exception('Para guardar cambios en una oferta existente, debe adjuntar al menos 1 documento (PDF o ZIP)');
            }

            // 2. Actualizar la oferta
            $oferta = Oferta::find($id);
            if (!$oferta) {
                throw new \Exception('Oferta no encontrada');
            }

            // Actualizar campos de la oferta
            $oferta->objeto = $_POST['objeto'] ?? '';
            $oferta->descripcion = $_POST['descripcion'] ?? '';
            $oferta->moneda = $_POST['moneda'] ?? '';
            $oferta->presupuesto = $_POST['presupuesto'] ?? 0;
            $oferta->actividad_id = $_POST['actividad_id'] ?? null;
            $oferta->fecha_inicio = $_POST['fecha_inicio'] ?? '';
            $oferta->hora_inicio = $_POST['hora_inicio'] ?? '';
            $oferta->fecha_cierre = $_POST['fecha_cierre'] ?? '';
            $oferta->hora_cierre = $_POST['hora_cierre'] ?? '';
            $oferta->estado = $_POST['estado'] ?? 'Pendiente';

            if (!$oferta->save()) {
                throw new \Exception('Error al actualizar la oferta');
            }

            // 3. Procesar nuevos documentos si los hay
            if ($nuevosArchivos > 0) {
                foreach ($_FILES['documentos']['name'] as $index => $nombre) {
                    if (empty($nombre) || $_FILES['documentos']['error'][$index] !== UPLOAD_ERR_OK) {
                        continue;
                    }

                    // Validar tipo de archivo
                    $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
                    $permitidos = ['pdf', 'zip'];

                    if (!in_array($extension, $permitidos)) {
                        throw new \Exception('Solo se permiten archivos PDF o ZIP');
                    }

                    // Validar tamaño (10MB)
                    if ($_FILES['documentos']['size'][$index] > 10485760) {
                        throw new \Exception('El archivo excede el tamaño máximo de 10MB');
                    }

                    // Crear nombre único
                    $nombreUnico = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $nombre);
                    $destino = 'uploads/' . $nombreUnico;

                    // Crear carpeta uploads si no existe
                    if (!is_dir('uploads')) {
                        mkdir('uploads', 0777, true);
                    }

                    // Mover archivo
                    if (!move_uploaded_file($_FILES['documentos']['tmp_name'][$index], $destino)) {
                        throw new \Exception('Error al subir el archivo: ' . $nombre);
                    }

                    // Guardar en BD
                    $documento = new Documento();
                    $documento->licitacion_id = $id;
                    $documento->titulo = $_POST['titulo_documento'][$index] ?? 'Documento';
                    $documento->descripcion = $_POST['descripcion_documento'][$index] ?? '';
                    $documento->archivo = $nombreUnico;

                    if (!$documento->save()) {
                        // Si falla guardar en BD, eliminar el archivo
                        unlink($destino);
                        throw new \Exception('Error al guardar el documento en la base de datos');
                    }
                }
            }

            // 4. Éxito
            if ($isAjax) {
                $this->json([
                    'success' => true,
                    'message' => 'Oferta actualizada correctamente',
                    'redirect' => BASE_URL . '/oferta/ver/' . $id
                ]);
            }

            $_SESSION['success'] = 'Oferta actualizada correctamente';
            header('Location: ' . BASE_URL . '/oferta/editar/' . $id);
            exit;
        } catch (\Exception $e) {
            if ($isAjax) {
                http_response_code(422);
                $this->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            // 5. Error
            $_SESSION['error'] = 'Error al guardar los cambios: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/oferta/editar/' . $id);
            exit;
        }
    }

    private function procesarDocumentos($oferta_id, $archivos)
    {
        // Implementación básica de subida de documentos
        $uploadDir = __DIR__ . '/../../uploads/documentos/';

        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($archivos as $key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Validar tipo de archivo
                $allowedTypes = ['application/pdf', 'application/zip'];
                $fileType = mime_content_type($file['tmp_name']);

                if (in_array($fileType, $allowedTypes)) {
                    // Generar nombre único
                    $fileName = uniqid() . '_' . basename($file['name']);
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($file['tmp_name'], $filePath)) {
                        // Guardar en base de datos (simplificado)
                        // En un sistema real usarías el modelo OfertaDocumento
                    }
                }
            }
        }
    }

    private function validarOferta($data)
    {
        if (empty($data['objeto']) || strlen(trim($data['objeto'])) > 150) {
            throw new \Exception('Objeto es requerido (máximo 150 caracteres)');
        }

        if (empty($data['descripcion']) || strlen(trim($data['descripcion'])) > 400) {
            throw new \Exception('Descripción es requerida (máximo 400 caracteres)');
        }

        if (empty($data['presupuesto']) || floatval($data['presupuesto']) <= 0) {
            throw new \Exception('Presupuesto debe ser mayor a 0');
        }

        // Validar fechas
        $inicio = strtotime($data['fecha_inicio'] . ' ' . $data['hora_inicio']);
        $cierre = strtotime($data['fecha_cierre'] . ' ' . $data['hora_cierre']);

        if ($cierre <= $inicio) {
            throw new \Exception('Fecha/hora de cierre debe ser posterior al inicio');
        }
    }
}
