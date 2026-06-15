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
        <p class="text-muted mb-4">Formulario con validacion reactiva en Vue.js y persistencia en backend PHP MVC.</p>
        
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?= $BASE_URL ?>/oferta/guardar" @submit="onSubmit">
            <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Objeto *</label>
                            <input type="text" name="objeto" class="form-control" maxlength="150" required v-model.trim="form.objeto"
                                   value="<?= htmlspecialchars($datos['objeto'] ?? '') ?>">
                            <small class="text-muted">Maximo 150 caracteres ({{ form.objeto.length }}/150)</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción / Alcance *</label>
                            <textarea name="descripcion" class="form-control" rows="3" maxlength="400" required v-model.trim="form.descripcion"><?= 
                                htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
                            <small class="text-muted">Maximo 400 caracteres ({{ form.descripcion.length }}/400)</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Moneda *</label>
                            <select name="moneda" class="form-select" required v-model="form.moneda">
                                <option value="COP" <?= ($datos['moneda'] ?? 'COP') == 'COP' ? 'selected' : '' ?>>COP - Peso Colombiano</option>
                                <option value="USD" <?= ($datos['moneda'] ?? '') == 'USD' ? 'selected' : '' ?>>USD - Dólar Estadounidense</option>
                                <option value="EUR" <?= ($datos['moneda'] ?? '') == 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Presupuesto *</label>
                            <input type="number" name="presupuesto" class="form-control" step="0.01" min="0" required v-model.number="form.presupuesto"
                                   value="<?= htmlspecialchars($datos['presupuesto'] ?? '0') ?>">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Actividad (UNSPSC) *</label>
                            <select name="actividad_id" class="form-select" required v-model="form.actividad_id">
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
                            <input type="date" name="fecha_inicio" class="form-control" required v-model="form.fecha_inicio"
                                   value="<?= $datos['fecha_inicio'] ?? date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora Inicio *</label>
                            <input type="time" name="hora_inicio" class="form-control" required v-model="form.hora_inicio"
                                   value="<?= $datos['hora_inicio'] ?? '08:00' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Cierre *</label>
                            <input type="date" name="fecha_cierre" class="form-control" required v-model="form.fecha_cierre"
                                   value="<?= $datos['fecha_cierre'] ?? date('Y-m-d', strtotime('+7 days')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hora Cierre *</label>
                            <input type="time" name="hora_cierre" class="form-control" required v-model="form.hora_cierre"
                                   value="<?= $datos['hora_cierre'] ?? '17:00' ?>">
                        </div>
                    </div>
                    <div class="alert alert-danger mt-2" v-if="errorFechas">
                        {{ errorFechas }}
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= $BASE_URL ?>/oferta" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success" :disabled="!formularioValido || enviando">
                    <span v-if="enviando" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span v-if="enviando"> Guardando...</span>
                    <span v-else>Guardar Oferta</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                enviando: false,
                form: {
                    objeto: <?= json_encode((string)($datos['objeto'] ?? '')) ?>,
                    descripcion: <?= json_encode((string)($datos['descripcion'] ?? '')) ?>,
                    moneda: <?= json_encode((string)($datos['moneda'] ?? 'COP')) ?>,
                    presupuesto: Number(<?= json_encode((string)($datos['presupuesto'] ?? '0')) ?>) || 0,
                    actividad_id: <?= json_encode((string)($datos['actividad_id'] ?? '')) ?>,
                    fecha_inicio: <?= json_encode((string)($datos['fecha_inicio'] ?? date('Y-m-d'))) ?>,
                    hora_inicio: <?= json_encode((string)($datos['hora_inicio'] ?? '08:00')) ?>,
                    fecha_cierre: <?= json_encode((string)($datos['fecha_cierre'] ?? date('Y-m-d', strtotime('+7 days')))) ?>,
                    hora_cierre: <?= json_encode((string)($datos['hora_cierre'] ?? '17:00')) ?>
                }
            },
            computed: {
                errorFechas() {
                    if (!this.form.fecha_inicio || !this.form.hora_inicio || !this.form.fecha_cierre || !this.form.hora_cierre) {
                        return '';
                    }

                    const inicio = new Date(`${this.form.fecha_inicio}T${this.form.hora_inicio}`);
                    const cierre = new Date(`${this.form.fecha_cierre}T${this.form.hora_cierre}`);

                    if (cierre <= inicio) {
                        return 'La fecha/hora de cierre debe ser posterior al inicio.';
                    }

                    return '';
                },
                formularioValido() {
                    return (
                        this.form.objeto.length > 0 &&
                        this.form.objeto.length <= 150 &&
                        this.form.descripcion.length > 0 &&
                        this.form.descripcion.length <= 400 &&
                        this.form.moneda.length > 0 &&
                        Number(this.form.presupuesto) > 0 &&
                        String(this.form.actividad_id).length > 0 &&
                        this.form.fecha_inicio.length > 0 &&
                        this.form.hora_inicio.length > 0 &&
                        this.form.fecha_cierre.length > 0 &&
                        this.form.hora_cierre.length > 0 &&
                        !this.errorFechas
                    );
                }
            },
            methods: {
                onSubmit(event) {
                    if (!this.formularioValido) {
                        event.preventDefault();
                        return;
                    }
                    this.enviando = true;
                }
            }
        });
    </script>
</body>
</html>