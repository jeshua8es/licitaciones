<?php
$result = $_SESSION['import_result'] ?? null;
unset($_SESSION['import_result']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Importar Actividades UNSPSC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= $BASE_URL ?>/dashboard">Sistema de Licitaciones</a>
            <div class="navbar-nav ms-auto flex-row">
                <a class="nav-link mx-2" href="<?= $BASE_URL ?>/dashboard">Dashboard</a>
                <a class="nav-link mx-2" href="<?= $BASE_URL ?>/ofertas">Ofertas</a>
                <a class="nav-link mx-2" href="<?= $BASE_URL ?>/ofertas/crear">Nueva Oferta</a>
                <a class="nav-link active mx-2" href="<?= $BASE_URL ?>/importar">Importar</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2>Importar Actividades UNSPSC</h2>
        
        <?php if ($result): ?>
            <?php if ($result['success']): ?>
            <div class="alert alert-success">
                Importación completada: <?= $result['importados'] ?> registros importados, 
                <?= $result['errores'] ?> errores.
            </div>
            <?php else: ?>
            <div class="alert alert-danger">
                Error: <?= $result['error'] ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?= $BASE_URL ?>/importar/procesar" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Archivo Excel UNSPSC</label>
                        <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls" required>
                        <small class="text-muted">
                            Descargar desde: 
                            <a href="https://a.storyblok.com/f/167454/x/8db69f44cd/unspcs-clasificador-de-bienes-y-servicios-de-naciones-unidas-en-espanol.xlsx" target="_blank">
                                enlace oficial UNSPSC
                            </a>
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">Importar</button>
                    <a href="<?= $BASE_URL ?>/dashboard" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>