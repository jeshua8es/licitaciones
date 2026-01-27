// public/js/components/FormCrearOferta.js
Vue.component('form-crear-oferta', {
    template: `
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">📝 Crear Nueva Oferta</h4>
            </div>
            <div class="card-body">
                <form @submit.prevent="guardarOferta">
                    <!-- Sección 1: Información Básica -->
                    <div class="mb-4">
                        <h5>📋 Información Básica</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Objeto *</label>
                                <input type="text" class="form-control" v-model="oferta.objeto" 
                                       maxlength="150" required>
                                <small class="text-muted">{{ oferta.objeto.length }}/150 caracteres</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Moneda *</label>
                                <select class="form-select" v-model="oferta.moneda" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="COP">COP - Peso Colombiano</option>
                                    <option value="USD">USD - Dólar Americano</option>
                                    <option value="EUR">EUR - Euro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descripción / Alcance *</label>
                            <textarea class="form-control" v-model="oferta.descripcion" 
                                      rows="3" maxlength="400" required></textarea>
                            <small class="text-muted">{{ oferta.descripcion.length }}/400 caracteres</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Presupuesto *</label>
                                <input type="number" class="form-control" v-model="oferta.presupuesto" 
                                       step="0.01" min="0" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Actividad *</label>
                                <select class="form-select" v-model="oferta.actividad_id" required>
                                    <option value="">Seleccionar actividad...</option>
                                    <option v-for="actividad in actividades" 
                                            :value="actividad.id">
                                        {{ actividad.codigo_segmento }} - {{ actividad.segmento }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección 2: Cronograma -->
                    <div class="mb-4">
                        <h5>📅 Cronograma</h5>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Fecha Inicio *</label>
                                <input type="date" class="form-control" v-model="oferta.fecha_inicio" required>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Hora Inicio *</label>
                                <input type="time" class="form-control" v-model="oferta.hora_inicio" required>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Fecha Cierre *</label>
                                <input type="date" class="form-control" v-model="oferta.fecha_cierre" required>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Hora Cierre *</label>
                                <input type="time" class="form-control" v-model="oferta.hora_cierre" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" @click="cancelar">
                            ❌ Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="guardando">
                            {{ guardando ? 'Guardando...' : '💾 Guardar Oferta' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `,
    data() {
        return {
            oferta: {
                objeto: '',
                descripcion: '',
                moneda: 'COP',
                presupuesto: 0,
                actividad_id: '',
                fecha_inicio: '',
                hora_inicio: '08:00',
                fecha_cierre: '',
                hora_cierre: '17:00'
            },
            actividades: [],
            guardando: false
        };
    },
    mounted() {
        this.cargarActividades();
    },
    methods: {
        cargarActividades() {
            axios.get(`${API_BASE}/actividades`)
                .then(response => {
                    this.actividades = response.data.data || [];
                })
                .catch(error => {
                    console.error('Error cargando actividades:', error);
                });
        },

        guardarOferta() {
            // Validaciones frontend
            if (!this.validarFormulario()) return;

            this.guardando = true;

            axios.post(`${API_BASE}/ofertas`, this.oferta)
                .then(response => {
                    alert('✅ Oferta creada exitosamente');
                    this.resetForm();
                    this.$emit('oferta-creada');
                })
                .catch(error => {
                    console.error('Error creando oferta:', error);
                    alert('❌ Error al crear oferta: ' + error.response.data.error);
                })
                .finally(() => {
                    this.guardando = false;
                });
        },

        validarFormulario() {
            // Validar que fecha inicio < fecha cierre
            if (this.oferta.fecha_inicio && this.oferta.fecha_cierre) {
                const inicio = new Date(this.oferta.fecha_inicio + ' ' + this.oferta.hora_inicio);
                const cierre = new Date(this.oferta.fecha_cierre + ' ' + this.oferta.hora_cierre);

                if (inicio >= cierre) {
                    alert('❌ La fecha/hora de inicio debe ser anterior a la fecha/hora de cierre');
                    return false;
                }
            }

            return true;
        },

        resetForm() {
            this.oferta = {
                objeto: '',
                descripcion: '',
                moneda: 'COP',
                presupuesto: 0,
                actividad_id: '',
                fecha_inicio: '',
                hora_inicio: '08:00',
                fecha_cierre: '',
                hora_cierre: '17:00'
            };
        },

        cancelar() {
            if (confirm('¿Cancelar creación de oferta? Los datos no guardados se perderán.')) {
                this.resetForm();
                this.$emit('cancelar');
            }
        }
    }
});