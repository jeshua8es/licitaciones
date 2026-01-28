<?php
session_start();
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../bootstrap/eloquent.php';
use App\Models\Actividad;
if (!isset($BASE_URL)) $BASE_URL = '/PHP/licitacion';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Actividades UNSPSC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $BASE_URL ?>">
                <i class="bi bi-house-gear"></i> Sistema de Licitaciones
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= $BASE_URL ?>/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link" href="<?= $BASE_URL ?>/ofertas">
                    <i class="bi bi-list-ul"></i> Ofertas
                </a>
                <a class="nav-link active" href="#">
                    <i class="bi bi-upload"></i> Cargar Actividades
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-database-add"></i> Cargar Actividades desde Excel UNSPSC
                        </h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Mensajes -->
                        <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Error:</strong> <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); endif; ?>
                        
                        <?php if (isset($_SESSION['import_result'])): 
                            $result = $_SESSION['import_result']; ?>
                        <div class="alert alert-<?= $result['success'] ? 'success' : 'warning' ?> alert-dismissible fade show">
                            <h5 class="alert-heading">
                                <i class="bi bi-<?= $result['success'] ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                                Resultado de la Importación
                            </h5>
                            <p><?= $result['message'] ?></p>
                            
                            <?php if (isset($result['data'])): ?>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <i class="bi bi-plus-circle"></i>
                                        <strong>Nuevas:</strong> <?= $result['data']['insertados'] ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-secondary">
                                        <i class="bi bi-arrow-clockwise"></i>
                                        <strong>Actualizadas:</strong> <?= $result['data']['actualizados'] ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($result['warning'])): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <?= $result['warning'] ?>
                            </div>
                            <?php endif; ?>
                            
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['import_result']); endif; ?>
                        
                        <!-- Instrucciones -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Instrucciones:</h6>
                            <ol class="mb-0">
                                <li>Descargue el archivo Excel UNSPSC desde el enlace oficial</li>
                                <li>Suba el archivo usando el formulario</li>
                                <li>El sistema importará las actividades automáticamente</li>
                            </ol>
                        </div>
                        
                        <!-- Enlace oficial -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-download"></i> Descargar Archivo Oficial</h6>
                            </div>
                            <div class="card-body">
                                <p>Descargue el clasificador UNSPSC de Naciones Unidas:</p>
                                <a href="https://a.storyblok.com/f/167454/x/8db69f44cd/unspcs-clasificador-de-bienes-y-servicios-de-naciones-unidas-en-espanol.xlsx" 
                                   target="_blank" class="btn btn-success">
                                    <i class="bi bi-file-earmark-excel"></i> Descargar UNSPSC.xlsx
                                </a>
                                <small class="d-block mt-2 text-muted">
                                    Este es el archivo requerido según las especificaciones del sistema.
                                </small>
                            </div>
                        </div>
                        
                        <!-- Formulario de carga -->
                        <form action="<?= $BASE_URL ?>/actividades/importar" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="archivo_excel" class="form-label">
                                    <strong>Archivo Excel *</strong>
                                </label>
                                <input type="file" class="form-control" id="archivo_excel" name="archivo_excel" 
                                       required accept=".xlsx,.xls">
                                <div class="form-text">
                                    Seleccione el archivo Excel descargado (.xlsx o .xls)
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cloud-upload"></i> Cargar Actividades
                                </button>
                                <a href="<?= $BASE_URL ?>/ofertas" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver a Ofertas
                                </a>
                            </div>
                        </form>
                        
                        <!-- Estadísticas actuales -->
                        <?php
                        try {
                            $totalActividades = Actividad::count();
                            ?>
                            <hr>
                            <div class="text-center">
                                <h6>Estado Actual:</h6>
                                <div class="display-6 <?= $totalActividades > 0 ? 'text-success' : 'text-warning' ?>">
                                    <?= $totalActividades ?> actividades cargadas
                                </div>
                                <?php if ($totalActividades > 0): ?>
                                <a href="<?= $BASE_URL ?>/ofertas/crear" class="btn btn-outline-success mt-2">
                                    <i class="bi bi-plus-circle"></i> Crear nueva oferta con actividades
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php
                        } catch (\Exception $e) {
                            echo '<div class="alert alert-warning">Tabla de actividades no disponible</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>