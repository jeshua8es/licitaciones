<?php
// app/views/ofertas/ver.php - VERSIÓN CORREGIDA
if (!isset($BASE_URL)) $BASE_URL = '/PHP/licitacion';
if (!isset($oferta)) die('Oferta no encontrada');

// Formatear fechas
$fecha_inicio = date('d/m/Y', strtotime($oferta['fecha_inicio']));
$hora_inicio = date('H:i', strtotime($oferta['hora_inicio']));
$fecha_cierre = date('d/m/Y', strtotime($oferta['fecha_cierre']));
$hora_cierre = date('H:i', strtotime($oferta['hora_cierre']));
$creado_en = date('d/m/Y H:i', strtotime($oferta['creado_en']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Oferta: <?= htmlspecialchars($oferta['consecutivo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Menú de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>/dashboard">
                <i class="bi bi-house-gear"></i> Sistema de Licitaciones
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= BASE_URL ?>/dashboard">Dashboard</a>
                <a class="nav-link" href="<?= BASE_URL ?>/oferta">Ofertas</a> <!-- CORREGIDO -->
                <a class="nav-link" href="<?= BASE_URL ?>/oferta/crear">Nueva Oferta</a> <!-- CORREGIDO -->
                <a class="nav-link" href="<?= BASE_URL ?>/importar">Importar</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">
                    <i class="bi bi-file-earmark-text"></i> 
                    Oferta: <?= htmlspecialchars($oferta['consecutivo']) ?>
                </h1>
                <p class="text-muted">Detalle completo de la licitación</p>
            </div>
            <div>
                <!-- BOTONES CORREGIDOS -->
                <a href="<?= BASE_URL ?>/oferta" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <a href="<?= BASE_URL ?>/oferta/editar/<?= $oferta['id'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            </div>
        </div>

        <!-- ... resto del código igual ... -->

        <!-- Botones de acción al final -->
        <div class="d-flex justify-content-between mt-4">
            <div>
                <a href="<?= BASE_URL ?>/oferta" class="btn btn-outline-secondary"> <!-- CORREGIDO -->
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/oferta/editar/<?= $oferta['id'] ?>" class="btn btn-warning"> <!-- CORREGIDO -->
                    <i class="bi bi-pencil"></i> Editar Oferta
                </a>
                <button type="button" class="btn btn-danger" 
                        onclick="confirmarEliminar(<?= $oferta['id'] ?>, '<?= htmlspecialchars($oferta['objeto']) ?>')">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmarEliminar(id, nombre) {
            document.getElementById('nombreOferta').textContent = nombre;
            // CORREGIDO también aquí:
            document.getElementById('btnEliminarConfirmar').href = '<?= BASE_URL ?>/oferta/eliminar/' + id;
            
            const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
            modal.show();
        }
    </script>
</body>
</html>