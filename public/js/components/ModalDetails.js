// Componente Modal Detalles
Vue.component('modal-details-component', {
    template: `
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true" ref="modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-alt me-2"></i>Detalles de Oferta
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="offer">
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h4 class="text-primary">{{ offer.objeto }}</h4>
                                    <p class="text-muted">{{ offer.descripcion }}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-dark fs-5 p-2">
                                        {{ offer.consecutivo }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-money-bill-wave me-2"></i>Información Financiera</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Moneda:</strong></td>
                                            <td>{{ offer.moneda }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Presupuesto:</strong></td>
                                            <td>{{ formatCurrency(offer.presupuesto) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-calendar-alt me-2"></i>Cronograma</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Inicio:</strong></td>
                                            <td>{{ formatDateTime(offer.fecha_inicio, offer.hora_inicio) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cierre:</strong></td>
                                            <td>{{ formatDateTime(offer.fecha_cierre, offer.hora_cierre) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-info-circle me-2"></i>Información General</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                <span class="status-badge" :class="getStatusClass(offer.estado)">
                                                    {{ offer.estado }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Creado:</strong></td>
                                            <td>{{ formatDate(offer.creado_en) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6" v-if="offer.documentos && offer.documentos.length > 0">
                                    <h6><i class="fas fa-file-alt me-2"></i>Documentos</h6>
                                    <div class="list-group">
                                        <div v-for="(doc, index) in offer.documentos" :key="index" 
                                             class="list-group-item document-item">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                            <strong>{{ doc.titulo }}</strong>
                                            <small class="text-muted d-block">{{ doc.descripcion }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="close">
                            <i class="fas fa-times me-1"></i> Cerrar
                        </button>
                        <button type="button" class="btn btn-primary" @click="edit">
                            <i class="fas fa-edit me-1"></i> Editar Oferta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `,

    props: {
        offer: {
            type: Object,
            default: null
        }
    },

    methods: {
        show() {
            const modal = new bootstrap.Modal(this.$refs.modal);
            modal.show();
        },

        close() {
            const modal = bootstrap.Modal.getInstance(this.$refs.modal);
            if (modal) {
                modal.hide();
            }
            this.$emit('close');
        },

        edit() {
            this.$emit('edit', this.offer);
            this.close();
        },

        getStatusClass(status) {
            const classes = {
                'Activa': 'status-activa',
                'En proceso': 'status-proceso',
                'Finalizada': 'status-finalizada',
                'Cancelada': 'status-cancelada'
            };
            return classes[status] || 'status-activa';
        },

        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return window.formatters ? window.formatters.formatDate(dateString) : dateString;
        },

        formatCurrency(amount) {
            if (!amount) return '$0.00';
            return window.formatters ? window.formatters.formatCurrency(amount) : `$${amount}`;
        },

        formatDateTime(date, time) {
            if (!date) return 'N/A';
            return `${this.formatDate(date)} ${time || ''}`;
        }
    }
});