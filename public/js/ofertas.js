// Configuración de API
const API_BASE = 'http://localhost/PHP/licitacion/api.php';
let currentOfertas = [];

// Cargar ofertas al iniciar
$(document).ready(function() {
    loadOfertas();
    loadCatalogos();
});

// Cargar todas las ofertas
async function loadOfertas(filters = {}) {
    try {
        let url = `${API_BASE}?url=ofertas`;

        // Aplicar filtros si existen
        if (Object.keys(filters).length > 0) {
            const params = new URLSearchParams(filters);
            url += `&${params.toString()}`;
        }

        const response = await fetch(url);
        const data = await response.json();

        currentOfertas = data;
        renderOfertasTable(data);

    } catch (error) {
        console.error('Error al cargar ofertas:', error);
        showAlert('Error al cargar las ofertas', 'danger');
    }
}

// Renderizar tabla de ofertas
function renderOfertasTable(ofertas) {
    const tableHtml = `
        <table class="table table-hover" id="ofertasTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Fecha Límite</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${ofertas.map(oferta => `
                    <tr>
                        <td>${oferta.id}</td>
                        <td>${oferta.titulo || ''}</td>
                        <td>${(oferta.descripcion || '').substring(0, 50)}...</td>
                        <td>
                            <span class="badge ${getStatusBadgeClass(oferta.estado)}">
                                ${oferta.estado || 'pendiente'}
                            </span>
                        </td>
                        <td>${oferta.fecha_limite ? new Date(oferta.fecha_limite).toLocaleDateString() : ''}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewDetails(${oferta.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="editOferta(${oferta.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteOferta(${oferta.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    $('#offers-table-container').html(tableHtml);
    
    // Inicializar DataTables si existe
    if ($.fn.DataTable) {
        $('#ofertasTable').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            }
        });
    }
}

// Clases para badges de estado
function getStatusBadgeClass(status) {
    const classes = {
        'activa': 'bg-success',
        'pendiente': 'bg-warning',
        'cerrada': 'bg-secondary',
        'cancelada': 'bg-danger'
    };
    return classes[status] || 'bg-info';
}

// Crear nueva oferta
async function createOferta(formData) {
    try {
        const response = await fetch(`${API_BASE}?url=ofertas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success || result.id) {
            showAlert('Oferta creada exitosamente', 'success');
            loadOfertas();
            return true;
        } else {
            showAlert('Error al crear la oferta', 'danger');
            return false;
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
        return false;
    }
}

// Actualizar oferta
async function updateOferta(id, formData) {
    try {
        const response = await fetch(`${API_BASE}?url=ofertas/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Oferta actualizada exitosamente', 'success');
            loadOfertas();
            return true;
        } else {
            showAlert('Error al actualizar la oferta', 'danger');
            return false;
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
        return false;
    }
}

// Eliminar oferta
async function deleteOferta(id) {
    if (!confirm('¿Está seguro de eliminar esta oferta?')) return;
    
    try {
        const response = await fetch(`${API_BASE}?url=ofertas/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Oferta eliminada exitosamente', 'success');
            loadOfertas();
        } else {
            showAlert('Error al eliminar la oferta', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión', 'danger');
    }
}

// Ver detalles
async function viewDetails(id) {
    try {
        const response = await fetch(`${API_BASE}?url=ofertas/${id}`);
        const oferta = await response.json();
        
        // Mostrar modal de detalles (usar ModalDetails.js)
        showDetailsModal(oferta);
    } catch (error) {
        console.error('Error:', error);
    }
}

// Editar oferta
async function editOferta(id) {
    try {
        const response = await fetch(`${API_BASE}?url=ofertas/${id}`);
        const oferta = await response.json();
        
        // Mostrar modal de edición
        showEditModal(oferta);
    } catch (error) {
        console.error('Error:', error);
    }
}

// Cargar catálogos para selects
async function loadCatalogos() {
    try {
        // Cargar segmentos, familias, clases, productos
        const [segments, families, classes, products] = await Promise.all([
            fetch(`${API_BASE}?url=segments`).then(r => r.json()),
            fetch(`${API_BASE}?url=families`).then(r => r.json()),
            fetch(`${API_BASE}?url=classes`).then(r => r.json()),
            fetch(`${API_BASE}?url=products`).then(r => r.json())
        ]);
        
        // Guardar en variables globales para usar en formularios
        window.catalogos = { segments, families, classes, products };
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
}

// Mostrar alerta
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insertar al inicio del contenedor principal
    $('.container-fluid').prepend(alertHtml);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

// Abrir modal de creación
function openCreateModal() {
    showCreateModal();
}