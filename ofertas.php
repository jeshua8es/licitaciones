<?php
// ofertas.php
require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Oferta;

// Obtener todas las ofertas
$ofertas = Oferta::all()->toArray();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Ofertas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Menú de Navegación -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container">
                <a class="navbar-brand" href="#">Sistema de Licitaciones</a>
                <div class="navbar-nav">
                    <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="nav-link active" href="ofertas.php"><i class="bi bi-list-ul"></i> Ofertas</a>
                    <a class="nav-link" href="crear-oferta.php"><i class="bi bi-plus-circle"></i> Nueva Oferta</a>
                    <a class="nav-link" href="importar-actividades.php"><i class="bi bi-upload"></i> Importar</a>
                </div>
            </div>
        </nav>

        <!-- Contenido Principal -->
        <div id="app" class="container">
            <!-- Tabla de Ofertas -->
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-table"></i> Listado de Ofertas</h4>
                    <div>
                        <a href="crear-oferta.php" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus-circle"></i> Nueva Oferta
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Consecutivo</th>
                                    <th>Objeto</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ofertas as $oferta): ?>
                                <tr>
                                    <td class="fw-bold"><?= $oferta['id'] ?></td>
                                    <td><?= $oferta['consecutivo'] ?? 'N/A' ?></td>
                                    <td><?= htmlspecialchars($oferta['objeto']) ?></td>
                                    <td><?= htmlspecialchars(substr($oferta['descripcion'], 0, 50)) ?>...</td>
                                    <td>
                                        <?php
                                        $badge_class = 'bg-secondary';
                                        if (isset($oferta['estado'])) {
                                            switch($oferta['estado']) {
                                                case 'activa': $badge_class = 'bg-success'; break;
                                                case 'pendiente': $badge_class = 'bg-warning'; break;
                                                case 'cerrada': $badge_class = 'bg-secondary'; break;
                                            }
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= $oferta['estado'] ?? 'pendiente' ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($oferta['creado_en'] ?? 'now')) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="detalle-oferta.php?id=<?= $oferta['id'] ?>" 
                                               class="btn btn-info" 
                                               title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="editar-oferta.php?id=<?= $oferta['id'] ?>" 
                                               class="btn btn-warning" 
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger" 
                                                    onclick="eliminarOferta(<?= $oferta['id'] ?>)"
                                                    title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($ofertas)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2">No hay ofertas registradas</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function eliminarOferta(id) {
            if (confirm('¿Está seguro de eliminar esta oferta?')) {
                fetch('api/ofertas.php?action=delete&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Oferta eliminada correctamente');
                            location.reload();
                        } else {
                            alert('Error al eliminar: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar la oferta');
                    });
            }
        }
    </script>
</body>
</html>