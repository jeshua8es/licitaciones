// Componente Header
Vue.component('header-component', {
    template: `
        <header class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-primary mb-2">
                        <i class="fas fa-file-contract me-2"></i>Sistema de Licitaciones
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>Prueba Técnica FullStack - Suplos 2025
                    </p>
                </div>
                <div class="text-end">
                    <div class="badge bg-primary fs-6 p-3">
                        <i class="fas fa-chart-bar me-2"></i>
                        {{ activeOffersCount }} ofertas activas
                    </div>
                </div>
            </div>
            <hr class="my-4">
        </header>
    `,
    props: {
        activeOffersCount: {
            type: Number,
            default: 0
        }
    }
});