// Utilidades de formateo
window.formatters = {

    // Formatear fecha
    formatDate: function(dateString) {
        if (!dateString) return 'N/A';

        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;

            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (error) {
            return dateString;
        }
    },

    // Formatear moneda
    formatCurrency: function(amount, currency = 'COP') {
        if (!amount && amount !== 0) return 'N/A';

        const formatter = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        return formatter.format(amount);
    },

    // Formatear fecha y hora
    formatDateTime: function(dateString, timeString) {
        const dateFormatted = this.formatDate(dateString);
        return timeString ? `${dateFormatted} ${timeString}` : dateFormatted;
    },

    // Recortar texto
    truncateText: function(text, maxLength = 100) {
        if (!text) return '';
        if (text.length <= maxLength) return text;

        return text.substring(0, maxLength) + '...';
    },

    // Capitalizar texto
    capitalize: function(text) {
        if (!text) return '';
        return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
    }
};