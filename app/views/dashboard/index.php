<?php
// app/views/dashboard/index.php
// El HTML que ya tienes, PERO asegúrate que $BASE_URL esté definida
if (!isset($BASE_URL)) {
    $BASE_URL = '/PHP/licitacion';
}
session_start();
// Mostrar mensaje de éxito o error si existe
$mensaje = '';
if (!empty($_SESSION['success'])) {
    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['success']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['success']);
} elseif (!empty($_SESSION['error'])) {
    $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Licitaciones - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }

        .card {
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            border-radius: 10px;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }

        .badge {
            font-size: 0.9em;
            padding: 5px 10px;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?= $mensaje ?>
    <!-- Menú de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $BASE_URL ?>/dashboard">
                <i class="bi bi-house-gear"></i> Sistema de Licitaciones
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="<?= $BASE_URL ?>/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link" href="<?= $BASE_URL ?>/oferta">
                    <i class="bi bi-list-ul"></i> Ofertas
                </a>
                <a class="nav-link" href="<?= $BASE_URL ?>/oferta/crear">
                    <i class="bi bi-plus-circle"></i> Nueva Oferta
                </a>
                <a class="nav-link" href="<?= $BASE_URL ?>/importar">
                    <i class="bi bi-upload"></i> Importar
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Encabezado -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6"><i class="bi bi-speedometer2"></i> Dashboard</h1>
                <p class="lead">Resumen general del sistema de licitaciones</p>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="row">
            <div class="col-md-3">
                <div class="card stat-card bg-primary">
                    <div class="card-body text-center">
                        <h2 class="display-4"><?= $total_ofertas ?></h2>
                        <p class="card-text">Total Ofertas</p>
                        <i class="bi bi-files" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success">
                    <div class="card-body text-center">
                        <h2 class="display-4"><?= $activas ?></h2>
                        <p class="card-text">Activas</p>
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h2 class="display-4"><?= $pendientes ?></h2>
                        <p class="card-text">Pendientes</p>
                        <i class="bi bi-clock" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-secondary">
                    <div class="card-body text-center">
                        <h2 class="display-4"><?= $cerradas ?></h2>
                        <p class="card-text">Cerradas</p>
                        <i class="bi bi-archive" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimas Ofertas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Últimas Ofertas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Consecutivo</th>
                                        <th>Objeto</th>
                                        <th>Estado</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimas_ofertas as $oferta): ?>
                                        <?php
                                        $badge_class = 'bg-secondary';
                                        if ($oferta['estado'] == 'activa') $badge_class = 'bg-success';
                                        if ($oferta['estado'] == 'pendiente') $badge_class = 'bg-warning';
                                        ?>
                                        <tr onclick="window.location='<?= BASE_URL ?>/oferta/ver/<?= $oferta['id'] ?>'" style="cursor: pointer;">
                                            <td class="fw-bold"><?= $oferta['consecutivo'] ?? 'N/A' ?></td>
                                            <td><?= htmlspecialchars($oferta['objeto']) ?></td>
                                            <td>
                                                <span class="badge <?= $badge_class ?>">
                                                    <?= ucfirst($oferta['estado']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($oferta['creado_en'])) ?></td>
                                            <td>
                                                <!-- BOTÓN VER - CORREGIDO -->
                                                <a href="<?= BASE_URL ?>/oferta/ver/<?= $oferta['id'] ?>"
                                                    class="btn btn-sm btn-info"
                                                    title="Ver detalle">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <!-- BOTÓN EDITAR  -->
                                                <a href="<?= BASE_URL ?>/oferta/editar/<?= $oferta['id'] ?>"
                                                    class="btn btn-sm btn-warning"
                                                    title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form action="<?php echo BASE_URL; ?>/oferta/eliminar/<?php echo $oferta['id']; ?>"
                                                    method="POST"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('¿Está seguro de eliminar la oferta <?= htmlspecialchars($oferta['consecutivo'] ?? '') ?>?');">
                                                    <button type="submit" class="btn btn-sm btn-danger">🗑️ Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($ultimas_ofertas)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                                <p class="mt-2">No hay ofertas registradas</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= $BASE_URL ?>/oferta" class="btn btn-primary">
                            <i class="bi bi-list-ul"></i> Ver todas las ofertas
                        </a>
                        <a href="<?= $BASE_URL ?>/oferta/crear" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Crear nueva oferta
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje del Sistema -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <strong>Sistema funcionando correctamente</strong>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>