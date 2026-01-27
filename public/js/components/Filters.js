// Componente Filtros
Vue.component('filters-component', {
    template: `
        <div class="card shadow-soft mb-4 border-left-primary">
            <div class="card-body">
                <h5 class="card-title text-primary mb-4">
                    <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buscar:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" 
                                   placeholder="Consecutivo, objeto o descripción..."
                                   :value="searchTerm"
                                   @input="$emit('update:searchTerm', $event.target.value)"
                                   @keyup.enter="$emit('filter')">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Estado:</label>
                        <select class="form-control" 
                                :value="selectedStatus"
                                @change="$emit('update:selectedStatus', $event.target.value)">
                            <option value="">Todos los estados</option>
                            <option value="Activa">Activa</option>
                            <option value="En proceso">En proceso</option>
                            <option value="Finalizada">Finalizada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button class="btn btn-primary flex-fill" @click="$emit('filter')">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                            <button class="btn btn-outline-secondary" @click="$emit('clear')">
                                <i class="fas fa-broom"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    props: {
        searchTerm: String,
        selectedStatus: String
    }
});