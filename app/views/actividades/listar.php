<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📋 Actividades UNSPSC Cargadas</h1>
        <a href="?controller=actividad&action=importar" class="btn btn-primary">
            ➕ Importar Nuevas Actividades
        </a>
    </div>

    <!-- Resumen -->
    <div class="alert alert-info">
        Total: <strong><?php echo count($actividades); ?></strong> actividades cargadas |
        Última importación: <?php echo $ultimaImportacion ?? 'N/A'; ?>
    </div>

    <!-- Tabla de actividades -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Segmento</th>
                    <th>Familia</th>
                    <th>Clase</th>
                    <th>Producto</th>
                    <th>Código Completo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividades as $actividad): ?>
                <tr>
                    <td><?php echo $actividad['id']; ?></td>
                    <td><?php echo htmlspecialchars($actividad['Segmento']); ?></td>
                    <td><?php echo htmlspecialchars($actividad['Familia']); ?></td>
                    <td><?php echo htmlspecialchars($actividad['Clase']); ?></td>
                    <td><?php echo htmlspecialchars($actividad['Producto']); ?></td>
                    <td>
                        <span class="badge bg-secondary">
                            <?php echo $actividad['codigo_segmento']; ?>-
                            <?php echo $actividad['codigo_familia']; ?>-
                            <?php echo $actividad['codigo_clase']; ?>-
                            <?php echo $actividad['codigo_producto']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Si no hay actividades -->
    <?php if (empty($actividades)): ?>
    <div class="text-center py-5">
        <div class="text-muted mb-3">
            <i class="fas fa-database fa-3x"></i>
        </div>
        <h4>No hay actividades cargadas</h4>
        <p>Importa actividades desde el archivo UNSPSC para comenzar</p>
        <a href="?controller=actividad&action=importar" class="btn btn-lg btn-primary">
            Importar Actividades
        </a>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>