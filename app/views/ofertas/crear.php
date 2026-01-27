<?php
// app/views/ofertas/crear.php
if (!isset($BASE_URL)) $BASE_URL = '/PHP/licitacion';
if (!isset($actividades)) $actividades = [];
if (!isset($error)) $error = '';
if (!isset($datos)) $datos = $_POST ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Oferta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= $BASE_URL ?>/dashboard">Sistema de Licitaciones</a>
            <div class="navbar-nav ms-auto flex-row">
                <a class="nav-link mx-2" href="<?= $BASE_URL ?>/dashboard">Dashboard</a>
                <a class="nav-link mx-2" href="<?= $BASE_URL ?>/oferta">Ofertas</a>
                <a class="nav-link active mx-2" href="<?= $BASE_URL ?>/oferta/crear">Nueva Oferta</a>
                <a class="nav-link mx-2" href="<?= $BASE_URL ?>/importar">Importar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4" id="app">
        <h2>Crear Nueva Oferta</h2>
        
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?= $BASE_URL ?>/oferta/guardar">
            <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Objeto *</label>
                            <input type="text" name="objeto" class="form-control" maxlength="150" required
                                   value="<?= htmlspecialchars($datos['objeto'] ?? '') ?>">
                            <small class="text-muted">Máximo 150 caracteres</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción / Alcance *</label>
                            <textarea name="descripcion" class="form-control" rows="3" maxlength="400" required><?= 
                                htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
                            <small class="text-muted">Máximo 400 caracteres</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Moneda *</label>
                            <select name="moneda" class="form-select" required>
                                <option value="COP" <?= ($datos['moneda'] ?? 'COP') == 'COP' ? 'selected' : '' ?>>COP - Peso Colombiano</option>
                                <option value="USD" <?= ($datos['moneda'] ?? '') == 'USD' ? 'selected' : '' ?>>USD - Dólar Estadounidense</option>
                                <option value="EUR" <?= ($datos['moneda'] ?? '') == 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Presupuesto *</label>
                            <input type="number" name="presupuesto" class="form-control" step="0.01" min="0" required
                                   value="<?= htmlspecialchars($datos['presupuesto'] ?? '0') ?>">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Actividad (UNSPSC) *</label>
                            <select name="actividad_id" class="form-select" required>
                                <option value="">Seleccionar actividad</option>
                                <?php foreach ($actividades as $actividad): ?>
                                <option value="<?= $actividad['id'] ?>" 
                                    <?= ($datos['actividad_id'] ?? '') == $actividad['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($actividad['producto']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN 2: CRONOGRAMA -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5>Cronograma</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio *</label>
                            <input type="date" name="fecha_inicio" class="form-control" required
                                   value="<?= $datos['fecha_inicio'] ?? date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora Inicio *</label>
                            <input type="time" name="hora_inicio" class="form-control" required
                                   value="<?= $datos['hora_inicio'] ?? '08:00' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Cierre *</label>
                            <input type="date" name="fecha_cierre" class="form-control" required
                                   value="<?= $datos['fecha_cierre'] ?? date('Y-m-d', strtotime('+7 days')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora Cierre *</label>
                            <input type="time" name="hora_cierre" class="form-control" required
                                   value="<?= $datos['hora_cierre'] ?? '17:00' ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= $BASE_URL ?>/oferta" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar Oferta</button>
            </div>
        </form>
    </div>
</body>
</html>