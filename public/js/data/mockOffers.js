// Datos mock de ofertas
window.mockOffers = [{
        id: 1,
        consecutivo: 'O-0001-25',
        objeto: 'Desarrollo de sistema de gestión de licitaciones en línea',
        descripcion: 'Desarrollo completo de una plataforma web para la gestión de procesos de licitación, incluyendo módulos de creación, seguimiento y evaluación de ofertas.',
        moneda: 'COP',
        presupuesto: 15000000,
        actividad_id: 1,
        fecha_inicio: '2025-01-15',
        hora_inicio: '08:00',
        fecha_cierre: '2025-02-28',
        hora_cierre: '17:00',
        estado: 'Activa',
        creado_en: '2025-01-10 10:30:00',
        documentos: [{
            titulo: 'Términos de Referencia',
            descripcion: 'Documento con los términos de referencia del proyecto',
            extension: 'PDF',
            fecha: '2025-01-10'
        }]
    },
    {
        id: 2,
        consecutivo: 'O-0002-25',
        objeto: 'Implementación de sistema de gestión documental',
        descripcion: 'Implementación de una plataforma para la gestión documental electrónica con firma digital y workflow de aprobación.',
        moneda: 'USD',
        presupuesto: 25000,
        actividad_id: 2,
        fecha_inicio: '2025-02-01',
        hora_inicio: '09:00',
        fecha_cierre: '2025-03-15',
        hora_cierre: '18:00',
        estado: 'En proceso',
        creado_en: '2025-01-25 14:45:00',
        documentos: [{
                titulo: 'Propuesta Técnica',
                descripcion: 'Documento con la propuesta técnica detallada',
                extension: 'PDF',
                fecha: '2025-01-25'
            },
            {
                titulo: 'Cronograma de Implementación',
                descripcion: 'Cronograma detallado del proyecto',
                extension: 'PDF',
                fecha: '2025-01-26'
            }
        ]
    },
    {
        id: 3,
        consecutivo: 'O-0003-25',
        objeto: 'Servicios de consultoría en ciberseguridad',
        descripcion: 'Auditoría y consultoría en seguridad de la información para identificar vulnerabilidades y establecer planes de mejora.',
        moneda: 'COP',
        presupuesto: 8000000,
        actividad_id: 4,
        fecha_inicio: '2025-01-10',
        hora_inicio: '10:00',
        fecha_cierre: '2025-01-31',
        hora_cierre: '16:00',
        estado: 'Finalizada',
        creado_en: '2025-01-05 09:15:00',
        documentos: [{
            titulo: 'Informe de Auditoría',
            descripcion: 'Informe completo de la auditoría de seguridad',
            extension: 'PDF',
            fecha: '2025-01-30'
        }]
    },
    {
        id: 4,
        consecutivo: 'O-0004-25',
        objeto: 'Mantenimiento de infraestructura de red',
        descripcion: 'Servicios de mantenimiento preventivo y correctivo para la infraestructura de red corporativa.',
        moneda: 'COP',
        presupuesto: 5000000,
        actividad_id: 3,
        fecha_inicio: '2025-03-01',
        hora_inicio: '08:30',
        fecha_cierre: '2025-03-31',
        hora_cierre: '17:30',
        estado: 'Activa',
        creado_en: '2025-02-15 11:20:00',
        documentos: [{
            titulo: 'Propuesta de Mantenimiento',
            descripcion: 'Propuesta técnica y económica',
            extension: 'PDF',
            fecha: '2025-02-15'
        }]
    }
];

console.log('📦 Datos mock cargados:', window.mockOffers.length, 'ofertas');