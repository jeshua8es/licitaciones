<?php
// crear-oferta.php
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Oferta;
use App\Models\Actividad;

// Obtener actividades para el select
$actividades = Actividad::all()->toArray();

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generar consecutivo
        $ultimaOferta = Oferta::orderBy('id', 'desc')->first();
        $numero = $ultimaOferta ? ((int) substr($ultimaOferta->consecutivo, 2, 4)) + 1 : 1;
        $consecutivo = 'O-' . str_pad($numero, 4, '0', STR_PAD_LEFT) . '-' . date('y');
        
        // Crear nueva oferta
        $oferta = new Oferta();
        $oferta->consecutivo = $consecutivo;
        $oferta->objeto = $_POST['objeto'] ?? '';
        $oferta->descripcion = $_POST['descripcion'] ?? '';
        $oferta->moneda = $_POST['moneda'] ?? 'COP';
        $oferta->presupuesto = $_POST['presupuesto'] ?? 0;
        $oferta->actividad_id = $_POST['actividad_id'] ?? null;
        $oferta->fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
        $oferta->hora_inicio = $_POST['hora_inicio'] ?? '08:00';
        $oferta->fecha_cierre = $_POST['fecha_cierre'] ?? date('Y-m-d');
        $oferta->hora_cierre = $_POST['hora_cierre'] ?? '17:00';
        $oferta->estado = 'pendiente';
        $oferta->creado_en = date('Y-m-d H:i:s');
        
        if ($oferta->save()) {
            $mensaje = "¡Oferta creada exitosamente! Consecutivo: $consecutivo";
            $tipoMensaje = 'success';
            
            // Redirigir después de 2 segundos
            header("refresh:2;url=ofertas.php");
        } else {
            $mensaje = "Error al guardar la oferta";
            $tipoMensaje = 'danger';
        }
    } catch (Exception $e) {
        $mensaje = "Error: " . $e->getMessage();
        $tipoMensaje = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Oferta</title>
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
                <a class="nav-link active" href="crear-oferta.php"><i class="bi bi-plus-circle"></i> Nueva Oferta</a>
                <a class="nav-link" href="importar-actividades.php"><i class="bi bi-upload"></i> Importar</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (isset($mensaje)): ?>
                <div class="alert alert-<?= $tipoMensaje ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="bi bi-file-earmark-plus"></i> Crear Nueva Oferta</h3>
                    </div>
                    
                    <form method="POST" id="form-oferta">
                        <div class="card-body">
                            
                            <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
                            <div class="section mb-4">
                                <h4 class="border-bottom pb-2 mb-3 text-primary">
                                    <i class="bi bi-info-circle"></i> Información Básica
                                </h4>
                                
                                <!-- Objeto -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Objeto de la Oferta <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="objeto" 
                                           class="form-control" 
                                           maxlength="150"
                                           required
                                           value="<?= $_POST['objeto'] ?? '' ?>">
                                    <div class="form-text">Máximo 150 caracteres</div>
                                </div>
                                
                                <!-- Descripción -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Descripción / Alcance <span class="text-danger">*</span></label>
                                    <textarea name="descripcion" 
                                              class="form-control" 
                                              rows="3"
                                              maxlength="400"
                                              required><?= $_POST['descripcion'] ?? '' ?></textarea>
                                    <div class="form-text">Máximo 400 caracteres</div>
                                </div>
                                
                                <!-- Moneda y Presupuesto -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Moneda <span class="text-danger">*</span></label>
                                        <select name="moneda" class="form-select" required>
                                            <option value="COP" <?= ($_POST['moneda'] ?? 'COP') === 'COP' ? 'selected' : '' ?>>COP - Peso Colombiano</option>
                                            <option value="USD" <?= ($_POST['moneda'] ?? '') === 'USD' ? 'selected' : '' ?>>USD - Dólar Estadounidense</option>
                                            <option value="EUR" <?= ($_POST['moneda'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Presupuesto <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" 
                                                   name="presupuesto" 
                                                   class="form-control"
                                                   step="0.01"
                                                   min="0"
                                                   required
                                                   value="<?= $_POST['presupuesto'] ?? 0 ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actividad -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Actividad <span class="text-danger">*</span></label>
                                    <select name="actividad_id" class="form-select" required>
                                        <option value="">Seleccione una actividad</option>
                                        <?php foreach ($actividades as $actividad): ?>
                                        <option value="<?= $actividad['id'] ?>" 
                                                <?= ($_POST['actividad_id'] ?? '') == $actividad['id'] ? 'selected' : '' ?>>
                                            [<?= $actividad['codigo_segmento'] ?>.<?= $actividad['codigo_familia'] ?>.<?= $actividad['codigo_clase'] ?>.<?= $actividad['codigo_producto'] ?>] 
                                            <?= htmlspecialchars($actividad['producto']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (empty($actividades)): ?>
                                    <div class="alert alert-warning mt-2">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        No hay actividades cargadas. 
                                        <a href="importar-actividades.php" class="alert-link">
                                            Importar actividades desde Excel
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- SECCIÓN 2: CRONOGRAMA -->
                            <div class="section mb-4">
                                <h4 class="border-bottom pb-2 mb-3 text-primary">
                                    <i class="bi bi-calendar-event"></i> Cronograma
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-header bg-info text-white">
                                                <h5 class="mb-0">Fecha y Hora de Inicio</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                                    <input type="date" 
                                                           name="fecha_inicio" 
                                                           class="form-control"
                                                           required
                                                           value="<?= $_POST['fecha_inicio'] ?? date('Y-m-d') ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Hora Inicio <span class="text-danger">*</span></label>
                                                    <input type="time" 
                                                           name="hora_inicio" 
                                                           class="form-control"
                                                           required
                                                           value="<?= $_POST['hora_inicio'] ?? '08:00' ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-header bg-warning text-dark">
                                                <h5 class="mb-0">Fecha y Hora de Cierre</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Fecha Cierre <span class="text-danger">*</span></label>
                                                    <input type="date" 
                                                           name="fecha_cierre" 
                                                           class="form-control"
                                                           required
                                                           value="<?= $_POST['fecha_cierre'] ?? date('Y-m-d', strtotime('+7 days')) ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Hora Cierre <span class="text-danger">*</span></label>
                                                    <input type="time" 
                                                           name="hora_cierre" 
                                                           class="form-control"
                                                           required
                                                           value="<?= $_POST['hora_cierre'] ?? '17:00' ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="ofertas.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Guardar Oferta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>