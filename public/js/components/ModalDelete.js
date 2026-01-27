// Componente Modal Eliminar
Vue.component('modal-delete-component', {
    template: `
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" ref="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="offer">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                <h5>¿Está seguro de eliminar esta oferta?</h5>
                                <p class="mb-0">Esta acción no se puede deshacer.</p>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">{{ offer.consecutivo }}</h6>
                                    <p class="card-text">{{ offer.objeto }}</p>
                                    <p class="card-text text-muted small">Estado: {{ offer.estado }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="close">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirm">
                            <i class="fas fa-trash me-1"></i> Eliminar Oferta
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

        confirm() {
            this.$emit('confirm', this.offer);
            this.close();
        }
    }
});