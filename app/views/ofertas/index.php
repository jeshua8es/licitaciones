<?php
// app/views/ofertas/index.php
if (!isset($BASE_URL)) $BASE_URL = '/PHP/licitacion';

// Asegurar que $ofertas esté definida
if (!isset($ofertas)) {
    $ofertas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Ofertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .table { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Listado de Ofertas</h1>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Consecutivo</th>
                    <th>Objeto</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ofertas && count($ofertas) > 0): ?>
                    <?php foreach ($ofertas as $oferta): ?>
                    <?php 
                    // Manejar tanto array como objeto
                    $id = is_array($oferta) ? $oferta['id'] : $oferta->id;
                    $consecutivo = is_array($oferta) ? $oferta['consecutivo'] : $oferta->consecutivo;
                    $objeto = is_array($oferta) ? $oferta['objeto'] : $oferta->objeto;
                    $estado = is_array($oferta) ? $oferta['estado'] : $oferta->estado;
                    $creado_en = is_array($oferta) ? $oferta['creado_en'] : $oferta->creado_en;
                    
                    $badge_class = 'bg-secondary';
                    if ($estado == 'activa') $badge_class = 'bg-success';
                    if ($estado == 'pendiente') $badge_class = 'bg-warning';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($consecutivo) ?></td>
                        <td><?= htmlspecialchars($objeto) ?></td>
                        <td>
                            <span class="badge <?= $badge_class ?>">
                                <?= ucfirst($estado) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($creado_en)) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/oferta/ver/<?= $id ?>" 
                               class="btn btn-sm btn-info">Ver</a>
                            <a href="<?= BASE_URL ?>/oferta/editar/<?= $id ?>" 
                               class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No hay ofertas registradas
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="mt-3">
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary">Volver al Dashboard</a>
            <a href="<?= BASE_URL ?>/oferta/crear" class="btn btn-success">Nueva Oferta</a>
        </div>
    </div>
</body>
</html>