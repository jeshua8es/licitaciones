function showCreateModal() {
    const modalHtml = `
        <div class="modal fade" id="createModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Nueva Oferta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="ofertaForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Título *</label>
                                        <input type="text" class="form-control" name="titulo" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" name="estado">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="activa">Activa</option>
                                            <option value="cerrada">Cerrada</option>
                                            <option value="cancelada">Cancelada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha Límite</label>
                                        <input type="date" class="form-control" name="fecha_limite">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Presupuesto</label>
                                        <input type="number" class="form-control" name="presupuesto" step="0.01">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Selects para catálogos -->
                            ${window.catalogos ? `
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Segmento</label>
                                            <select class="form-select" name="segment_id">
                                                <option value="">Seleccionar...</option>
                                                ${window.catalogos.segments.map(s => 
                                                    `<option value="${s.id}">${s.nombre}</option>`
                                                ).join('')}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Familia</label>
                                            <select class="form-select" name="family_id">
                                                <option value="">Seleccionar...</option>
                                                ${window.catalogos.families.map(f => 
                                                    `<option value="${f.id}">${f.nombre}</option>`
                                                ).join('')}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Clase</label>
                                            <select class="form-select" name="class_id">
                                                <option value="">Seleccionar...</option>
                                                ${window.catalogos.classes.map(c => 
                                                    `<option value="${c.id}">${c.nombre}</option>`
                                                ).join('')}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Producto</label>
                                            <select class="form-select" name="product_id">
                                                <option value="">Seleccionar...</option>
                                                ${window.catalogos.products.map(p => 
                                                    `<option value="${p.id}">${p.nombre}</option>`
                                                ).join('')}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="submitOfertaForm()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#modal-container').html(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('createModal'));
    modal.show();
}

function submitOfertaForm() {
    const formData = {};
    $('#ofertaForm').serializeArray().forEach(item => {
        formData[item.name] = item.value;
    });
    
    // Llamar a la función createOferta del archivo ofertas.js
    if (window.createOferta) {
        window.createOferta(formData).then(success => {
            if (success) {
                bootstrap.Modal.getInstance(document.getElementById('createModal')).hide();
            }
        });
    }
}