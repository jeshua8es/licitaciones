<?php
// app/views/ofertas/editar.php
if (!isset($BASE_URL)) $BASE_URL = '/PHP/licitacion';
if (!isset($oferta)) die('Oferta no encontrada');
if (!isset($actividades)) $actividades = [];
if (!isset($error)) $error = '';
if (!isset($documentos)) $documentos = [];

// Asumir que $oferta es un array (usar toArray() si es objeto Eloquent)
if (is_object($oferta)) {
    $oferta = $oferta->toArray();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Oferta: <?= htmlspecialchars($oferta['consecutivo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .documento-item { border-left: 4px solid #0d6efd; }
        .file-upload { border: 2px dashed #dee2e6; border-radius: 5px; padding: 20px; text-align: center; }
        .file-upload.dragover { border-color: #0d6efd; background-color: #f8f9fa; }
        .character-counter { font-size: 0.8rem; }
        .character-counter.warning { color: #ffc107; }
        .character-counter.danger { color: #dc3545; }
    </style>
</head>

<body>
    <!-- Menú de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $BASE_URL ?>/dashboard">
                <i class="bi bi-house-gear"></i> Sistema de Licitaciones
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= $BASE_URL ?>/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link" href="<?= $BASE_URL ?>/ofertas">
                    <i class="bi bi-list-ul"></i> Ofertas
                </a>
                <a class="nav-link active" href="#">
                    <i class="bi bi-pencil"></i> Editar Oferta
                </a>
                <a class="nav-link" href="<?= $BASE_URL ?>/importar">
                    <i class="bi bi-upload"></i> Importar
                </a>
            </div>
        </div>
    </nav>

    <div class="container" id="app">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">
                    <i class="bi bi-pencil-square"></i> 
                    Editar Oferta: <?= htmlspecialchars($oferta['consecutivo']) ?>
                </h1>
                <p class="text-muted">Modifique los datos de la licitación</p>
            </div>
            <div>
                <a href="<?= $BASE_URL ?>/ofertas/ver/<?= $oferta['id'] ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulario Principal -->
        <form method="POST" action="<?= $BASE_URL ?>/ofertas/actualizar/<?= $oferta['id'] ?>" 
              enctype="multipart/form-data" id="formPrincipal">
            
            <!-- Campos de archivo REALES (ocultos, serán llenados por Vue) -->
            <div id="campos-archivos-reales" style="display: none;"></div>
            
            <!-- Campo para contar documentos -->
            <input type="hidden" name="total_documentos" id="total_documentos" value="0">
            
            <!-- SECCIÓN 1: INFORMACIÓN BÁSICA -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información Básica</h5>
                    <span class="badge bg-light text-primary">Sección 1 de 3</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Consecutivo (solo lectura) -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Consecutivo</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($oferta['consecutivo']) ?>" readonly>
                            <small class="text-muted">Generado automáticamente por el sistema</small>
                        </div>
                        
                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado *</label>
                            <select name="estado" class="form-select" v-model="estado" required>
                                <option value="activa" <?= $oferta['estado'] == 'activa' ? 'selected' : '' ?>>Activa</option>
                                <option value="pendiente" <?= $oferta['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="cerrada" <?= $oferta['estado'] == 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                            </select>
                        </div>
                        
                        <!-- Objeto -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Objeto de la Licitación *</label>
                            <input type="text" name="objeto" class="form-control" 
                                   v-model="objeto" 
                                   @input="actualizarContador('objeto')"
                                   maxlength="150" 
                                   required
                                   value="<?= htmlspecialchars($oferta['objeto']) ?>">
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Describa claramente el propósito de la licitación</small>
                                <small class="character-counter" :class="objetoClass">
                                    {{ objeto.length }}/150 caracteres
                                </small>
                            </div>
                        </div>
                        
                        <!-- Descripción -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Descripción / Alcance *</label>
                            <textarea name="descripcion" class="form-control" rows="4"
                                      v-model="descripcion"
                                      @input="actualizarContador('descripcion')"
                                      maxlength="400" 
                                      required><?= htmlspecialchars($oferta['descripcion']) ?></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Detalle los requisitos, especificaciones y condiciones</small>
                                <small class="character-counter" :class="descripcionClass">
                                    {{ descripcion.length }}/400 caracteres
                                </small>
                            </div>
                        </div>
                        
                        <!-- Moneda y Presupuesto -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Moneda *</label>
                            <select name="moneda" class="form-select" v-model="moneda" required>
                                <option value="COP" <?= $oferta['moneda'] == 'COP' ? 'selected' : '' ?>>COP - Peso Colombiano</option>
                                <option value="USD" <?= $oferta['moneda'] == 'USD' ? 'selected' : '' ?>>USD - Dólar Estadounidense</option>
                                <option value="EUR" <?= $oferta['moneda'] == 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Presupuesto *</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ moneda }}</span>
                                <input type="number" name="presupuesto" class="form-control"
                                       v-model="presupuesto"
                                       step="0.01" 
                                       min="0" 
                                       required
                                       value="<?= $oferta['presupuesto'] ?>">
                            </div>
                            <small class="text-muted">Valor estimado para la licitación</small>
                        </div>
                        
                        <!-- Actividad -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Actividad (Clasificador UNSPSC) *</label>
                            <select name="actividad_id" class="form-select" v-model="actividad_id" required>
                                <option value="">Seleccione una actividad</option>
                                <?php foreach ($actividades as $actividad): ?>
                                <?php 
                                $selected = $oferta['actividad_id'] == $actividad['id'] ? 'selected' : '';
                                $texto = "[" . $actividad['codigo_segmento'] . "." . 
                                         $actividad['codigo_familia'] . "." . 
                                         $actividad['codigo_clase'] . "." . 
                                         $actividad['codigo_producto'] . "] " . 
                                         $actividad['producto'];
                                ?>
                                <option value="<?= $actividad['id'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($texto) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Clasificación según estándar UNSPSC de Naciones Unidas</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN 2: CRONOGRAMA -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Cronograma</h5>
                    <span class="badge bg-light text-info">Sección 2 de 3</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Fecha y Hora Inicio -->
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="bi bi-play-circle"></i> Fecha y Hora de Inicio</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fecha Inicio *</label>
                                        <input type="date" name="fecha_inicio" class="form-control"
                                               v-model="fecha_inicio"
                                               :min="fechaHoy"
                                               required
                                               value="<?= $oferta['fecha_inicio'] ?>">
                                        <small class="text-muted">Fecha de apertura del proceso</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Hora Inicio *</label>
                                        <input type="time" name="hora_inicio" class="form-control"
                                               v-model="hora_inicio"
                                               required
                                               value="<?= $oferta['hora_inicio'] ?>">
                                        <small class="text-muted">Hora exacta de inicio (formato 24h)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fecha y Hora Cierre -->
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="bi bi-stop-circle"></i> Fecha y Hora de Cierre</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fecha Cierre *</label>
                                        <input type="date" name="fecha_cierre" class="form-control"
                                               v-model="fecha_cierre"
                                               :min="fecha_inicio"
                                               required
                                               value="<?= $oferta['fecha_cierre'] ?>">
                                        <small class="text-muted">Fecha límite para presentar ofertas</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Hora Cierre *</label>
                                        <input type="time" name="hora_cierre" class="form-control"
                                               v-model="hora_cierre"
                                               required
                                               value="<?= $oferta['hora_cierre'] ?>">
                                        <small class="text-muted">Hora exacta de cierre (formato 24h)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Validación de fechas -->
                    <div v-if="validarFechas" class="alert alert-danger mt-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ validarFechas }}
                    </div>
                    
                    <!-- Resumen de plazos -->
                    <div v-if="!validarFechas && fecha_inicio && fecha_cierre" class="alert alert-light mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Inicio:</small>
                                <div class="fw-bold">{{ formatearFecha(fecha_inicio) }} {{ hora_inicio }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Cierre:</small>
                                <div class="fw-bold">{{ formatearFecha(fecha_cierre) }} {{ hora_cierre }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SECCIÓN 3: DOCUMENTOS -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-files"></i> Documentos Adjuntos</h5>
                    <span class="badge bg-light text-warning">Sección 3 de 3</span>
                </div>
                <div class="card-body">
                    <!-- Mensaje sobre documentos -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Importante:</strong> Debe adjuntar al menos 1 documento (PDF o ZIP) para guardar los cambios.
                        <br>
                        <small>Formatos permitidos: .pdf, .zip. Tamaño máximo: 10MB por archivo.</small>
                    </div>
                    
                    <!-- Lista de Documentos Actuales -->
                    <h6 class="border-bottom pb-2 mb-3">Documentos Actuales</h6>
                    <div v-if="documentos.length > 0">
                        <div v-for="(doc, index) in documentos" :key="index" class="mb-2">
                            <div class="d-flex justify-content-between align-items-center p-2 documento-item bg-light rounded">
                                <div>
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    <strong>{{ doc.titulo }}</strong>
                                    <small class="text-muted ms-2">{{ doc.descripcion }}</small>
                                    <br>
                                    <small v-if="doc.archivoNombre" class="text-muted">
                                        <i class="bi bi-file-earmark"></i> {{ doc.archivoNombre }}
                                    </small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            @click="eliminarDocumento(index)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4 text-muted">
                        <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                        <p class="mt-2">No hay documentos adjuntos</p>
                    </div>
                    
                    <!-- Formulario para Nuevo Documento -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4">Agregar Nuevo Documento</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Título del Documento *</label>
                            <input type="text" v-model="nuevoDocumento.titulo" class="form-control" maxlength="100" placeholder="Ej: Términos de Referencia">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Descripción</label>
                            <input type="text" v-model="nuevoDocumento.descripcion" class="form-control" maxlength="200" placeholder="Descripción breve del documento">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Archivo *</label>
                            <input type="file" ref="archivoInput" @change="seleccionarArchivo" class="form-control" accept=".pdf,.zip">
                            <small class="text-muted" v-if="nuevoDocumento.archivoNombre">
                                {{ nuevoDocumento.archivoNombre }}
                            </small>
                        </div>
                    </div>
                    
                    <!-- Área de arrastrar y soltar -->
                    <div class="file-upload mt-3" 
                         @dragover.prevent="dragover = true" 
                         @dragleave="dragover = false"
                         @drop.prevent="manejarDrop"
                         :class="{ 'dragover': dragover }"
                         @click="seleccionarArchivoDesdeDrop">
                        <i class="bi bi-cloud-arrow-up" style="font-size: 2rem;"></i>
                        <p class="mt-2">Arrastre y suelte archivos aquí</p>
                        <small class="text-muted">o haga clic para seleccionar</small>
                        <input type="file" class="d-none" ref="dropInput" @change="seleccionarArchivoDrop" accept=".pdf,.zip">
                    </div>
                    
                    <!-- Botón para agregar documento -->
                    <div class="text-end mt-3">
                        <button type="button" class="btn btn-primary" @click="agregarDocumento" 
                                :disabled="!documentoValido">
                            <i class="bi bi-plus-circle"></i> Agregar Documento
                        </button>
                    </div>
                    
                    <!-- Contador de documentos -->
                    <div class="mt-3 text-center">
                        <span class="badge" :class="documentos.length >= 1 ? 'bg-success' : 'bg-danger'">
                            {{ documentos.length }} documento(s) adjunto(s)
                        </span>
                        <small class="text-muted ms-2">Mínimo requerido: 1</small>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="d-flex justify-content-between mt-4">
                <div>
                    <a href="<?= $BASE_URL ?>/ofertas/ver/<?= $oferta['id'] ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al detalle
                    </a>
                </div>
                <div>
                    <button type="button" class="btn btn-success" @click="validarFormulario" :disabled="!formularioValido || guardando">
                        <span v-if="guardando">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            Guardando...
                        </span>
                        <span v-else>
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        new Vue({
            el: '#app',
            data: {
                // Datos del formulario
                objeto: '<?= addslashes($oferta['objeto']) ?>',
                descripcion: '<?= addslashes($oferta['descripcion']) ?>',
                moneda: '<?= $oferta['moneda'] ?>',
                presupuesto: <?= $oferta['presupuesto'] ?>,
                actividad_id: '<?= $oferta['actividad_id'] ?>',
                estado: '<?= $oferta['estado'] ?>',
                fecha_inicio: '<?= $oferta['fecha_inicio'] ?>',
                hora_inicio: '<?= $oferta['hora_inicio'] ?>',
                fecha_cierre: '<?= $oferta['fecha_cierre'] ?>',
                hora_cierre: '<?= $oferta['hora_cierre'] ?>',
                
                // Documentos
                documentos: <?= json_encode($documentos) ?>,
                nuevoDocumento: {
                    titulo: '',
                    descripcion: '',
                    archivo: null,
                    archivoNombre: ''
                },
                dragover: false,
                
                // Estado
                guardando: false,
                fechaHoy: new Date().toISOString().split('T')[0]
            },
            computed: {
                objetoClass() {
                    const length = this.objeto.length;
                    if (length >= 140) return 'danger';
                    if (length >= 120) return 'warning';
                    return '';
                },
                descripcionClass() {
                    const length = this.descripcion.length;
                    if (length >= 380) return 'danger';
                    if (length >= 350) return 'warning';
                    return '';
                },
                validarFechas() {
                    if (!this.fecha_inicio || !this.fecha_cierre) return '';
                    
                    const inicio = new Date(this.fecha_inicio + 'T' + this.hora_inicio);
                    const cierre = new Date(this.fecha_cierre + 'T' + this.hora_cierre);
                    
                    if (cierre <= inicio) {
                        return 'La fecha/hora de cierre debe ser posterior a la fecha/hora de inicio';
                    }
                    
                    return '';
                },
                documentoValido() {
                    return this.nuevoDocumento.titulo && 
                           this.nuevoDocumento.titulo.trim().length > 0 &&
                           this.nuevoDocumento.archivo && 
                           this.nuevoDocumento.titulo.length <= 100 &&
                           (!this.nuevoDocumento.descripcion || this.nuevoDocumento.descripcion.length <= 200);
                },
                formularioValido() {
                    return this.objeto && 
                           this.objeto.trim().length > 0 &&
                           this.objeto.length <= 150 &&
                           this.descripcion && 
                           this.descripcion.trim().length > 0 &&
                           this.descripcion.length <= 400 &&
                           this.moneda &&
                           this.presupuesto > 0 &&
                           this.actividad_id &&
                           this.fecha_inicio &&
                           this.hora_inicio &&
                           this.fecha_cierre &&
                           this.hora_cierre &&
                           !this.validarFechas &&
                           this.documentos.length >= 1; // REQUISITO: al menos 1 documento
                }
            },
            methods: {
                actualizarContador(campo) {
                    // Solo para reactividad
                },
                formatearFecha(fecha) {
                    if (!fecha) return '';
                    const [year, month, day] = fecha.split('-');
                    return `${day}/${month}/${year}`;
                },
                seleccionarArchivo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.validarArchivo(file);
                    }
                },
                seleccionarArchivoDrop(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.validarArchivo(file);
                    }
                },
                seleccionarArchivoDesdeDrop() {
                    this.$refs.dropInput.click();
                },
                manejarDrop(event) {
                    this.dragover = false;
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        this.validarArchivo(file);
                    }
                },
                validarArchivo(file) {
                    const tiposPermitidos = ['application/pdf', 'application/zip', 'application/x-zip-compressed'];
                    const extension = file.name.split('.').pop().toLowerCase();
                    
                    if (!tiposPermitidos.includes(file.type) && !['pdf', 'zip'].includes(extension)) {
                        alert('Solo se permiten archivos PDF o ZIP');
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) { // 10MB
                        alert('El archivo no debe superar los 10MB');
                        return;
                    }
                    
                    this.nuevoDocumento.archivo = file;
                    this.nuevoDocumento.archivoNombre = file.name;
                },
                agregarDocumento() {
                    if (!this.documentoValido) {
                        alert('Complete todos los campos requeridos del documento');
                        return;
                    }
                    
                    this.documentos.push({
                        titulo: this.nuevoDocumento.titulo,
                        descripcion: this.nuevoDocumento.descripcion,
                        archivoNombre: this.nuevoDocumento.archivoNombre,
                        archivoFile: this.nuevoDocumento.archivo,
                        esNuevo: true
                    });
                    
                    // Limpiar formulario
                    this.nuevoDocumento = {
                        titulo: '',
                        descripcion: '',
                        archivo: null,
                        archivoNombre: ''
                    };
                    
                    // Limpiar input de archivo
                    if (this.$refs.archivoInput) {
                        this.$refs.archivoInput.value = '';
                    }
                    if (this.$refs.dropInput) {
                        this.$refs.dropInput.value = '';
                    }
                },
                eliminarDocumento(index) {
                    if (confirm('¿Está seguro de eliminar este documento?')) {
                        this.documentos.splice(index, 1);
                    }
                },
                validarFormulario(event) {
                    if (!this.formularioValido) {
                        alert('Por favor complete todos los campos requeridos correctamente.');
                        return;
                    }

                    if (this.documentos.length < 1) {
                        alert('Debe adjuntar al menos 1 documento.');
                        return;
                    }

                    this.guardando = true;

                    // 1. Crear FormData
                    const formData = new FormData();
                    
                    // 2. Agregar campos normales del formulario
                    const formElements = document.getElementById('formPrincipal').elements;
                    
                    for (let element of formElements) {
                        if (element.name && !element.disabled && element.type !== 'file' && element.type !== 'submit' && element.type !== 'button') {
                            if (element.type === 'checkbox' || element.type === 'radio') {
                                if (element.checked) {
                                    formData.append(element.name, element.value);
                                }
                            } else {
                                formData.append(element.name, element.value);
                            }
                        }
                    }
                    
                    // 3. Agregar documentos REALES al FormData
                    const archivosContainer = document.getElementById('campos-archivos-reales');
                    archivosContainer.innerHTML = '';
                    
                    let contadorNuevos = 0;
                    this.documentos.forEach((doc, index) => {
                        if (doc.esNuevo && doc.archivoFile) {
                            // Es un archivo nuevo que se va a subir
                            formData.append(`nuevos_documentos[${contadorNuevos}][titulo]`, doc.titulo);
                            formData.append(`nuevos_documentos[${contadorNuevos}][descripcion]`, doc.descripcion || '');
                            formData.append(`archivos[${contadorNuevos}]`, doc.archivoFile);
                            contadorNuevos++;
                        }
                    });
                    
                    // 4. Agregar contador
                    formData.append('total_nuevos_documentos', contadorNuevos);
                    document.getElementById('total_documentos').value = this.documentos.length;
                    formData.append('total_documentos', this.documentos.length);
                    
                    // 5. Enviar mediante AJAX
                    axios.post('<?= $BASE_URL ?>/ofertas/actualizar/<?= $oferta['id'] ?>', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        console.log('Respuesta:', response.data);
                        if (response.data.success) {
                            window.location.href = '<?= $BASE_URL ?>/ofertas/ver/<?= $oferta['id'] ?>';
                        } else {
                            alert('Error: ' + (response.data.message || 'Error desconocido'));
                            this.guardando = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error completo:', error);
                        console.error('Respuesta error:', error.response);
                        
                        let mensajeError = 'Error al guardar los cambios';
                        if (error.response) {
                            if (error.response.data && error.response.data.message) {
                                mensajeError = error.response.data.message;
                            } else if (error.response.data && error.response.data.error) {
                                mensajeError = error.response.data.error;
                            } else if (error.response.status === 422) {
                                mensajeError = 'Error de validación: ' + JSON.stringify(error.response.data.errors);
                            }
                        }
                        
                        alert(mensajeError);
                        this.guardando = false;
                    });
                }
            },
            mounted() {
                // Inicializar fecha mínima para cierre
                if (this.fecha_cierre && this.fecha_inicio > this.fecha_cierre) {
                    this.fecha_cierre = this.fecha_inicio;
                }
            }
        });
    </script>
</body>
</html>