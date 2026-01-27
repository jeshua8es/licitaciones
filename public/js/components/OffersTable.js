// Componente Tabla de Ofertas
Vue.component('offers-table-component', {
    template: `
        <div class="card shadow-soft">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="12%" class="ps-4">Consecutivo</th>
                                <th width="22%">Objeto</th>
                                <th width="30%">Descripción</th>
                                <th width="10%">Fecha Inicio</th>
                                <th width="10%">Fecha Cierre</th>
                                <th width="8%">Estado</th>
                                <th width="8%" class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="offer in offers" :key="offer.id" class="cursor-pointer" @click="$emit('view', offer)">
                                <td class="ps-4">
                                    <span class="badge bg-dark">
                                        <i class="fas fa-hashtag me-1"></i>{{ offer.consecutivo }}
                                    </span>
                                </td>
                                <td class="fw-semibold">{{ offer.objeto }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ offer.descripcion ? offer.descripcion.substring(0, 70) + '...' : 'Sin descripción' }}
                                    </small>
                                </td>
                                <td>{{ formatDate(offer.fecha_inicio) }}</td>
                                <td>{{ formatDate(offer.fecha_cierre) }}</td>
                                <td>
                                    <span class="status-badge" :class="getStatusClass(offer.estado)">
                                        {{ offer.estado }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm" @click.stop>
                                        <button class="btn btn-outline-info me-1" 
                                                @click="$emit('view', offer)"
                                                title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning me-1" 
                                                @click="$emit('edit', offer)"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" 
                                                @click="$emit('delete', offer)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Mensaje si no hay ofertas -->
                            <tr v-if="offers.length === 0">
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                        <h5>No se encontraron ofertas</h5>
                                        <p class="mb-3">Intenta con otros filtros o crea una nueva oferta</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `,
    props: {
        offers: {
            type: Array,
            default: () => []
        }
    },
    methods: {
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
        }
    }
});