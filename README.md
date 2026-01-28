# Sistema de Licitaciones

## Descripción
Módulo de licitaciones en línea desarrollado con PHP 7.0+ (MVC puro) + Vue.js 2.6+ + Bootstrap 5.2+. (se utilizo xammp con mysql)

## Características implementadas
Dashboard con estadísticas  
Listado de ofertas con paginación  
CRUD completo (Crear, Leer, Actualizar, Eliminar)  
Generación automática de consecutivo (O-0001-25)  
Validaciones frontend (Vue.js) y backend (PHP)  
Arquitectura MVC con Eloquent ORM  
Sistema de enrutamiento propio  

## endpoints
GET	/dashboard	Dashboard principal
GET	/oferta	Listado completo
GET	/oferta/ver/{id}	Detalle de oferta
GET	/oferta/crear	Formulario creación
GET	/oferta/editar/{id}	Formulario edición
POST	/oferta/guardar	Guardar nueva
PUT	/oferta/actualizar/{id}	Actualizar existente
DELETE	/oferta/eliminar/{id}	Eliminar oferta

## Instalación

1. Clonar repositorio
# 1. Clonar en servidor
git clone https://github.com/jeshua8es/licitaciones.git /var/www/licitaciones

# 2. Configurar permisos
chown -R www-data:www-data /var/www/licitaciones
chmod -R 755 /var/www/licitaciones/storage

# 3. Configurar virtual host
* Importar `database/schema.sql` en MySQL
* Configurar conexión en `config/database.php`
* Acceder a `http://localhost/licitacion/`

URL: http://localhost/licitaciones/

Dashboard: http://localhost/licitaciones/dashboard

Usuario demo: Ver datos en database/database.sql

## Rutas principales
- `/` o `/dashboard` - Dashboard principal
- `/oferta/ver/{id}` - Ver detalle de oferta
- `/oferta/editor/{id}` - Editar oferta
- `/oferta/eliminar/{id}` - Eliminar oferta (POST)

## Tecnologías
- **Backend**: PHP 7.0+ (sin frameworks), MVC, Eloquent ORM
- **Frontend**: Vue.js 2.6+, Axios, Bootstrap 5.2+
- **Base de datos**: MySQL/MariaDB