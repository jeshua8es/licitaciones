// Aplicación principal Vue.js
console.log('🚀 app.js cargado - Sistema de Licitaciones');

// Configuración global
Vue.config.devtools = true;
Vue.config.productionTip = false;

// Crear la instancia principal
const app = new Vue({
    el: '#app',
    data: {
        loading: false,
        offers: [],
        filteredOffers: [],
        selectedOffer: null,
        offerToDelete: null,
        searchTerm: '',
        selectedStatus: '',
        modalMode: 'create' // 'create' o 'edit'
    },

    created() {
        console.log('📦 Creada instancia Vue');
        this.loadInitialData();
    },

    mounted() {
        console.log('✅ Vue montado en #app');

        // Ocultar spinner después de 1 segundo
        setTimeout(() => {
            this.loading = false;
        }, 500);
    },

    computed: {
        activeOffersCount() {
            return this.offers.filter(o => o.estado === 'Activa').length;
        }
    },

    methods: {
        loadInitialData() {
            console.log('📥 Cargando datos iniciales...');

            // Usar datos mock iniciales
            this.offers = window.mockOffers || [];
            this.filteredOffers = [...this.offers];

            console.log(`✅ ${this.offers.length} ofertas cargadas`);
        },

        applyFilters() {
            console.log('🔍 Aplicando filtros...');

            let filtered = [...this.offers];

            // Filtrar por término de búsqueda
            if (this.searchTerm.trim()) {
                const term = this.searchTerm.toLowerCase().trim();
                filtered = filtered.filter(offer =>
                    offer.consecutivo.toLowerCase().includes(term) ||
                    offer.objeto.toLowerCase().includes(term) ||
                    (offer.descripcion && offer.descripcion.toLowerCase().includes(term))
                );
            }

            // Filtrar por estado
            if (this.selectedStatus) {
                filtered = filtered.filter(offer => offer.estado === this.selectedStatus);
            }

            this.filteredOffers = filtered;
            console.log(`📊 ${filtered.length} ofertas después de filtrar`);
        },

        clearFilters() {
            this.searchTerm = '';
            this.selectedStatus = '';
            this.filteredOffers = [...this.offers];
            console.log('🧹 Filtros limpiados');
        },

        openCreateModal() {
            console.log('📝 Abriendo modal para crear oferta');
            this.modalMode = 'create';
            this.selectedOffer = null;

            // Mostrar modal usando ref
            if (this.$refs.modalForm) {
                this.$refs.modalForm.show();
            }
        },

        openEditModal(offer) {
            console.log('✏️ Abriendo modal para editar oferta:', offer.consecutivo);
            this.modalMode = 'edit';
            this.selectedOffer = {...offer };

            if (this.$refs.modalForm) {
                this.$refs.modalForm.show();
            }
        },

        saveOffer(offerData) {
            console.log('💾 Guardando oferta:', offerData);

            if (this.modalMode === 'create') {
                // Generar nuevo ID y consecutivo
                const nextId = this.offers.length > 0 ?
                    Math.max(...this.offers.map(o => o.id)) + 1 :
                    1;

                const year = new Date().getFullYear().toString().slice(-2);
                const consecutivo = `O-${String(nextId).padStart(4, '0')}-${year}`;

                const newOffer = {
                    id: nextId,
                    consecutivo: consecutivo,
                    ...offerData,
                    estado: 'Activa',
                    creado_en: new Date().toISOString().slice(0, 19).replace('T', ' ')
                };

                // Agregar a la lista
                this.offers.unshift(newOffer);
                this.filteredOffers.unshift(newOffer);

                alert(`✅ Oferta creada exitosamente: ${consecutivo}`);

            } else {
                // Actualizar oferta existente
                const index = this.offers.findIndex(o => o.id === offerData.id);
                if (index !== -1) {
                    this.offers[index] = {...this.offers[index], ...offerData };
                    this.applyFilters(); // Re-aplicar filtros

                    alert(`✅ Oferta ${offerData.consecutivo} actualizada`);
                }
            }
        },

        viewDetails(offer) {
            console.log('👁️ Viendo detalles de:', offer.consecutivo);
            this.selectedOffer = {...offer };

            if (this.$refs.modalDetails) {
                this.$refs.modalDetails.show();
            }
        },

        confirmDelete(offer) {
            console.log('🗑️ Confirmando eliminación de:', offer.consecutivo);
            this.offerToDelete = {...offer };

            if (this.$refs.modalDelete) {
                this.$refs.modalDelete.show();
            }
        },

        deleteOffer(offer) {
            console.log('🗑️ Eliminando oferta:', offer.consecutivo);

            const index = this.offers.findIndex(o => o.id === offer.id);
            if (index !== -1) {
                this.offers.splice(index, 1);
                this.applyFilters(); // Re-aplicar filtros

                alert(`✅ Oferta ${offer.consecutivo} eliminada`);
            }

            this.offerToDelete = null;
        },

        exportToExcel() {
            console.log('📊 Exportando a Excel...');

            // Simular exportación
            const dataStr = JSON.stringify(this.filteredOffers, null, 2);
            const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);

            const exportFileDefaultName = `ofertas_${new Date().toISOString().slice(0,10)}.json`;

            const linkElement = document.createElement('a');
            linkElement.setAttribute('href', dataUri);
            linkElement.setAttribute('download', exportFileDefaultName);
            linkElement.click();

            alert(`📥 Exportadas ${this.filteredOffers.length} ofertas (simulación)`);
        },

        printTable() {
            window.print();
        },

        closeModal() {
            console.log('❌ Modal cerrado');
            this.selectedOffer = null;
        },

        closeDetails() {
            console.log('❌ Modal de detalles cerrado');
            this.selectedOffer = null;
        },

        closeDeleteModal() {
            console.log('❌ Modal de eliminación cerrado');
            this.offerToDelete = null;
        }
    }
});

// Hacer la instancia global para depuración
window.vueApp = app;
console.log('🎯 Aplicación Vue lista y asignada a window.vueApp');