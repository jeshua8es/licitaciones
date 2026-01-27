<?php
// importar-actividades.php
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Actividad;

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    require_once __DIR__ . '/libs/PHPExcel/PHPExcel.php';
    
    try {
        $archivo = $_FILES['archivo']['tmp_name'];
        
        $objPHPExcel = PHPExcel_IOFactory::load($archivo);
        $sheet = $objPHPExcel->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        
        $importados = 0;
        $errores = 0;
        
        // Limpiar tabla si se solicita
        if (isset($_POST['limpiar_anteriores'])) {
            Actividad::query()->delete();
        }
        
        // Empezar desde la fila 2 (asumiendo que la fila 1 son encabezados)
        for ($row = 2; $row <= $highestRow; $row++) {
            try {
                $actividad = new Actividad();
                $actividad->codigo_segmento = $sheet->getCell("A{$row}")->getValue();
                $actividad->segmento = $sheet->getCell("B{$row}")->getValue();
                $actividad->codigo_familia = $sheet->getCell("C{$row}")->getValue();
                $actividad->familia = $sheet->getCell("D{$row}")->getValue();
                $actividad->codigo_clase = $sheet->getCell("E{$row}")->getValue();
                $actividad->clase = $sheet->getCell("F{$row}")->getValue();
                $actividad->codigo_producto = $sheet->getCell("G{$row}")->getValue();
                $actividad->producto = $sheet->getCell("H{$row}")->getValue();
                
                if ($actividad->save()) {
                    $importados++;
                } else {
                    $errores++;
                }
            } catch (Exception $e) {
                $errores++;
            }
        }
        
        $mensaje = "Importación completada. Registros importados: $importados, Errores: $errores";
        $tipoMensaje = 'success';
        
    } catch (Exception $e) {
        $mensaje = "Error al importar: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Actividades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema de Licitaciones</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a class="nav-link" href="ofertas.php"><i class="bi bi-list-ul"></i> Ofertas</a>
                <a class="nav-link" href="crear-oferta.php"><i class="bi bi-plus-circle"></i> Nueva Oferta</a>
                <a class="nav-link active" href="importar-actividades.php"><i class="bi bi-upload"></i> Importar</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($mensaje): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="mb-0"><i class="bi bi-upload"></i> Importar Actividades UNSPSC</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle-fill"></i> Instrucciones</h5>
                            <p>Descargue el archivo Excel desde 
                                <a href="https://a.storyblok.com/f/167454/x/8db69f44cd/unspcs-clasificador-de-bienes-y-servicios-de-naciones-unidas-en-espanol.xlsx" 
                                   target="_blank" class="alert-link">este enlace</a>
                            </p>
                        </div>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Seleccionar Archivo Excel</label>
                                <input type="file" 
                                       name="archivo" 
                                       class="form-control"
                                       accept=".xlsx,.xls"
                                       required>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="limpiar_anteriores" id="limpiarCheck">
                                    <label class="form-check-label" for="limpiarCheck">
                                        Eliminar actividades anteriores
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="ofertas.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-upload"></i> Importar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>