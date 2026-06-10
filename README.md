# Sistema de Licitaciones

## Descripción
Módulo de licitaciones desarrollado con PHP 7+ en arquitectura MVC, sin framework fullstack, usando Eloquent ORM para persistencia y Vue.js para interacciones de frontend.

## Características
- Dashboard con estadísticas de ofertas y actividades.
- CRUD de ofertas: crear, listar, ver, editar y eliminar.
- Generación automática de consecutivo tipo O-0001-26.
- Validaciones en frontend y backend.
- Carga e importación de actividades UNSPSC desde Excel.
- Gestión de documentos por oferta (PDF y ZIP).

## Tecnologías
- Backend: PHP 7+, MVC propio, Eloquent.
- Frontend: Vue.js 2.6, Axios, Bootstrap 5.
- Base de datos: MySQL / MariaDB.

## Requisitos
- PHP 7.4 o superior.
- Composer.
- MySQL o MariaDB.
- Apache (XAMPP recomendado para entorno local).

## Instalación
1. Clonar el repositorio.
2. Ejecutar composer install en la raíz del proyecto.
3. Crear la base de datos licitaciones.
4. Importar el script SQL ubicado en database/licitaciones.sql.
5. Configurar credenciales de base de datos en config/database.php.
6. Levantar Apache y MySQL.
7. Acceder a la aplicación en:
	http://localhost/PHP/licitacion/

## Rutas Web principales
- GET /dashboard
- GET /oferta
- GET /oferta/crear
- POST /oferta/guardar
- GET /oferta/ver/{id}
- GET /oferta/editar/{id}
- POST /oferta/actualizar/{id}
- POST /oferta/eliminar/{id}

## API disponible
Archivo de entrada: routes/api.php

Rutas principales:
- GET ofertas
- GET ofertas/{id}
- POST ofertas
- PUT ofertas/{id}
- DELETE ofertas/{id}
- GET ofertas/{id}/documentos
- POST ofertas/{id}/documentos

## Entregables del proyecto
- Código fuente en repositorio Git.
- Carpeta bd con script SQL de creación.
- Este README con instrucciones de ejecución.