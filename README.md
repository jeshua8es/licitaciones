# Sistema de Licitaciones

## Descripción
Módulo de licitaciones en línea desarrollado con PHP 7.0+ (MVC puro) + Vue.js 2.6+ + Bootstrap 5.2+.

## Características implementadas
Dashboard con estadísticas  
Listado de ofertas con paginación  
CRUD completo (Crear, Leer, Actualizar, Eliminar)  
Generación automática de consecutivo (O-0001-25)  
Validaciones frontend (Vue.js) y backend (PHP)  
Arquitectura MVC con Eloquent ORM  
Sistema de enrutamiento propio  

## Instalación rápida

1. Clonar repositorio
2. Importar `database/schema.sql` en MySQL
3. Configurar conexión en `config/database.php`
4. Acceder a `http://localhost/licitacion/`

## Rutas principales
- `/` o `/dashboard` - Dashboard principal
- `/oferta/ver/{id}` - Ver detalle de oferta
- `/oferta/editor/{id}` - Editar oferta
- `/oferta/eliminar/{id}` - Eliminar oferta (POST)

## Tecnologías
- **Backend**: PHP 7.0+ (sin frameworks), MVC, Eloquent ORM
- **Frontend**: Vue.js 2.6+, Axios, Bootstrap 5.2+
- **Base de datos**: MySQL/MariaDB