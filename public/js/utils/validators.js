// Utilidades de validación
window.validators = {

    // Validar objeto (máx 150 caracteres)
    validateObjeto: function(objeto) {
        if (!objeto || objeto.trim().length === 0) {
            return { valid: false, message: 'El objeto es requerido' };
        }

        if (objeto.length > 150) {
            return { valid: false, message: 'El objeto no puede exceder 150 caracteres' };
        }

        return { valid: true };
    },

    // Validar descripción (máx 400 caracteres)
    validateDescripcion: function(descripcion) {
        if (!descripcion || descripcion.trim().length === 0) {
            return { valid: false, message: 'La descripción es requerida' };
        }

        if (descripcion.length > 400) {
            return { valid: false, message: 'La descripción no puede exceder 400 caracteres' };
        }

        return { valid: true };
    },

    // Validar presupuesto (mayor a 0)
    validatePresupuesto: function(presupuesto) {
        if (!presupuesto && presupuesto !== 0) {
            return { valid: false, message: 'El presupuesto es requerido' };
        }

        if (presupuesto <= 0) {
            return { valid: false, message: 'El presupuesto debe ser mayor a 0' };
        }

        // Validar máximo 2 decimales
        const decimalCount = (presupuesto.toString().split('.')[1] || '').length;
        if (decimalCount > 2) {
            return { valid: false, message: 'El presupuesto no puede tener más de 2 decimales' };
        }

        return { valid: true };
    },

    // Validar fechas (inicio < cierre)
    validateFechas: function(fechaInicio, fechaCierre, horaInicio, horaCierre) {
        if (!fechaInicio) {
            return { valid: false, message: 'La fecha de inicio es requerida' };
        }

        if (!fechaCierre) {
            return { valid: false, message: 'La fecha de cierre es requerida' };
        }

        const startDate = new Date(`${fechaInicio}T${horaInicio || '00:00'}`);
        const endDate = new Date(`${fechaCierre}T${horaCierre || '23:59'}`);

        if (endDate <= startDate) {
            return { valid: false, message: 'La fecha/hora de cierre debe ser posterior a la fecha/hora de inicio' };
        }

        return { valid: true };
    },

    // Validar archivo (PDF o ZIP, máximo 10MB)
    validateArchivo: function(file) {
        if (!file) {
            return { valid: false, message: 'El archivo es requerido' };
        }

        // Validar tipo
        const validTypes = ['application/pdf', 'application/zip'];
        const validExtensions = ['.pdf', '.zip'];

        const fileExtension = file.name.toLowerCase().slice(file.name.lastIndexOf('.'));

        if (!validTypes.includes(file.type) && !validExtensions.includes(fileExtension)) {
            return { valid: false, message: 'Solo se permiten archivos PDF o ZIP' };
        }

        // Validar tamaño (10MB)
        if (file.size > 10 * 1024 * 1024) {
            return { valid: false, message: 'El archivo no debe exceder 10MB' };
        }

        return { valid: true };
    },

    // Validar documento para edición (mínimo 1)
    validateDocumentos: function(documentos) {
        if (!documentos || documentos.length === 0) {
            return { valid: false, message: 'Debe cargar al menos 1 documento para editar la oferta' };
        }

        return { valid: true };
    }
};