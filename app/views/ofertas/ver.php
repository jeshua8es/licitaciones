<?php
if (!isset($BASE_URL)) {
    $BASE_URL = '/PHP/licitacion';
}

if (!isset($oferta)) {
    die('Oferta no encontrada');
}

if (is_object($oferta)) {
    $oferta = $oferta->toArray();
}

$fecha_inicio = !empty($oferta['fecha_inicio']) ? date('d/m/Y', strtotime($oferta['fecha_inicio'])) : 'N/A';
$hora_inicio = !empty($oferta['hora_inicio']) ? date('H:i', strtotime($oferta['hora_inicio'])) : 'N/A';
$fecha_cierre = !empty($oferta['fecha_cierre']) ? date('d/m/Y', strtotime($oferta['fecha_cierre'])) : 'N/A';
$hora_cierre = !empty($oferta['hora_cierre']) ? date('H:i', strtotime($oferta['hora_cierre'])) : 'N/A';
$creado_en = !empty($oferta['creado_en']) ? date('d/m/Y H:i', strtotime($oferta['creado_en'])) : 'N/A';

$badge_class = 'bg-secondary';
if (($oferta['estado'] ?? '') === 'activa') {
    $badge_class = 'bg-success';
} elseif (($oferta['estado'] ?? '') === 'pendiente') {
    $badge_class = 'bg-warning text-dark';
} elseif (($oferta['estado'] ?? '') === 'cerrada') {
    $badge_class = 'bg-dark';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Oferta: <?= htmlspecialchars($oferta['consecutivo'] ?? 'N/A') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $BASE_URL ?>/dashboard">
                <i class="bi bi-house-gear"></i> Sistema de Licitaciones
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= $BASE_URL ?>/dashboard">Dashboard</a>
                <a class="nav-link" href="<?= $BASE_URL ?>/oferta">Ofertas</a>
                <a class="nav-link" href="<?= $BASE_URL ?>/oferta/crear">Nueva Oferta</a>
                <a class="nav-link" href="<?= $BASE_URL ?>/importar">Importar</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-file-earmark-text"></i>
                    Oferta <?= htmlspecialchars($oferta['consecutivo'] ?? 'N/A') ?>
                </h1>
                <p class="text-muted mb-0">Detalle completo de la licitación</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $BASE_URL ?>/oferta" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <a href="<?= $BASE_URL ?>/oferta/editar/<?= (int) ($oferta['id'] ?? 0) ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong>Información de la oferta</strong>
                <span class="badge <?= $badge_class ?>"><?= htmlspecialchars(ucfirst($oferta['estado'] ?? 'sin estado')) ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Consecutivo</label>
                        <div class="fw-bold"><?= htmlspecialchars($oferta['consecutivo'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Fecha de creación</label>
                        <div><?= htmlspecialchars($creado_en) ?></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Objeto</label>
                        <div><?= htmlspecialchars($oferta['objeto'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Descripción</label>
                        <div><?= nl2br(htmlspecialchars($oferta['descripcion'] ?? 'N/A')) ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Moneda</label>
                        <div><?= htmlspecialchars($oferta['moneda'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Presupuesto</label>
                        <div>$ <?= number_format((float) ($oferta['presupuesto'] ?? 0), 2, ',', '.') ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">Actividad ID</label>
                        <div><?= htmlspecialchars((string) ($oferta['actividad_id'] ?? 'N/A')) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Inicio</label>
                        <div><?= htmlspecialchars($fecha_inicio) ?> <?= htmlspecialchars($hora_inicio) ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Cierre</label>
                        <div><?= htmlspecialchars($fecha_cierre) ?> <?= htmlspecialchars($hora_cierre) ?></div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="<?= $BASE_URL ?>/oferta" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul"></i> Volver al listado
                </a>
                <div class="d-flex gap-2">
                    <a href="<?= $BASE_URL ?>/oferta/editar/<?= (int) ($oferta['id'] ?? 0) ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar oferta
                    </a>
                    <form action="<?= $BASE_URL ?>/oferta/eliminar/<?= (int) ($oferta['id'] ?? 0) ?>" method="POST" onsubmit="return confirm('¿Seguro que desea eliminar esta oferta?');">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>