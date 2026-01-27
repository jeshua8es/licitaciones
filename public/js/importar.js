$(document).ready(function() {
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        importFile();
    });
});

async function importFile() {
    const fileInput = $('#fileInput')[0];
    const importType = $('#importType').val();

    if (!fileInput.files[0]) {
        alert('Por favor seleccione un archivo');
        return;
    }

    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('type', importType);

    try {
        const response = await fetch('http://localhost/PHP/licitacion/import.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showImportResults(result);
            showAlert(`Importación exitosa: ${result.count} registros procesados`, 'success');
        } else {
            showAlert(`Error en importación: ${result.error}`, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión al servidor', 'danger');
    }
}

function showImportResults(result) {
    const resultsHtml = `
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Resultados de Importación</h6>
            </div>
            <div class="card-body">
                <p><strong>Total registros:</strong> ${result.count}</p>
                <p><strong>Correctos:</strong> ${result.success_count}</p>
                <p><strong>Errores:</strong> ${result.error_count}</p>
                
                ${result.errors && result.errors.length > 0 ? `
                    <div class="mt-3">
                        <h6>Errores encontrados:</h6>
                        <ul class="list-group">
                            ${result.errors.map(error => `
                                <li class="list-group-item list-group-item-danger">
                                    Línea ${error.line}: ${error.message}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    $('#importResults').html(resultsHtml);
}