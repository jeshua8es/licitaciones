// Componente Botones de Acción
Vue.component('action-buttons-component', {
    template: `
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-list me-2"></i>Ofertas Registradas
            </h4>
            <div>
                <button class="btn btn-primary me-2" @click="$emit('create')">
                    <i class="fas fa-plus me-1"></i> Crear Oferta
                </button>
                <button class="btn btn-success me-2" @click="$emit('export')">
                    <i class="fas fa-file-excel me-1"></i> Exportar Excel
                </button>
                <button class="btn btn-secondary" @click="$emit('print')">
                    <i class="fas fa-print me-1"></i> Imprimir
                </button>
            </div>
        </div>
    `
});