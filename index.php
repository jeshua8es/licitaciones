<?php
// index.php - ENRUTADOR MVC COMPATIBLE CON ELOQUENT
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// EN EL ENCABEZADO de index.php, después de session_start();


error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);
session_start();

// ==================== CONFIGURACIÓN ====================
define('BASE_PATH', dirname(__FILE__));
define('APP_PATH', BASE_PATH . '/app');
define('VIEWS_PATH', APP_PATH . '/views');

// URL base
$base_dir = '/PHP/licitacion';
define('BASE_URL', $base_dir);

// ==================== INICIALIZAR ELOQUENT ====================
require_once BASE_PATH . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Cargar configuración de database.php
$databaseConfig = require BASE_PATH . '/config/database.php';
$defaultConnection = $databaseConfig['default'];
$connectionConfig = $databaseConfig['connections'][$defaultConnection];

// Configurar Eloquent ORM
$capsule = new Capsule;
$capsule->addConnection($connectionConfig);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// ==================== AUTOCARGADOR ====================
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// ==================== ENRUTAMIENTO ====================
$request = $_SERVER['REQUEST_URI'];
$route = str_replace($base_dir, '', $request);
$route = parse_url($route, PHP_URL_PATH);

// Quitar slash inicial
$route = ltrim($route, '/');

$rutasEspecificas = [
    'actividades/cargar' => ['ActividadController', 'mostrarFormulario'],
    'actividades/importar' => ['ActividadController', 'importar'],
    'actividades/listar' => ['ActividadController', 'listar'],
    'actividades' => ['ActividadController', 'index'], // Para AJAX
];

// Verificar si la ruta coincide con alguna ruta específica
foreach ($rutasEspecificas as $rutaPatron => $controladorAccion) {
    if ($route === $rutaPatron) {
        $controller_name = $controladorAccion[0];
        $action = $controladorAccion[1];
        $id = null;
        
        // Cargar controlador
        $controller_file = APP_PATH . '/controllers/' . $controller_name . '.php';
        
        if (file_exists($controller_file)) {
            require_once $controller_file;
            $controller_class = 'App\\Controllers\\' . $controller_name;
            
            if (class_exists($controller_class)) {
                $controller = new $controller_class();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    exit; // Importante: salir después de ejecutar
                }
            }
        }
        break;
    }
}

// Ruta por defecto
if (empty($route)) {
    $route = 'dashboard';
}


// Dividir la ruta
$route_parts = explode('/', trim($route, '/'));
$controller_name = !empty($route_parts[0]) ? ucfirst($route_parts[0]) . 'Controller' : 'DashboardController';
$action = isset($route_parts[1]) ? $route_parts[1] : 'index';
$id = isset($route_parts[2]) ? $route_parts[2] : null;

// DEBUG TEMPORAL - QUITAR DESPUÉS
error_log("RUTA DEBUG: route='$route', controller='$controller_name', action='$action', id='$id'");


// MAPEO DE ACCIONES (SOLUCIÓN PARA 'editor' -> 'editar')
$actionMappings = [
    'editor' => 'editar',  // Corrige 'editor' a 'editar'
    'show' => 'ver',       // También puedes mapear show a ver
    'view' => 'ver',
    'create' => 'crear',
    'store' => 'guardar',
    'update' => 'actualizar',
    'delete' => 'eliminar'
];

// Aplicar mapeo si es necesario
if (array_key_exists($action, $actionMappings)) {
    $action = $actionMappings[$action];
}

// ==================== CARGAR CONTROLADOR ====================
$controller_file = APP_PATH . '/controllers/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
    
    $controller_class = 'App\\Controllers\\' . $controller_name;
    
    if (class_exists($controller_class)) {
        $controller = new $controller_class();
        
        if (method_exists($controller, $action)) {
            try {
                if ($id) {
                    $controller->$action($id);
                } else {
                    $controller->$action();
                }
            } catch (Exception $e) {
                echo "<h1>Error</h1><p>" . $e->getMessage() . "</p>";
            }
        } else {
            http_response_code(404);
            echo "<h1>404 - Acción no encontrada</h1>";
        }
    } else {
        http_response_code(404);
        echo "<h1>404 - Controlador no encontrado</h1>";
    }
} else {
    // Si no es un controlador, verificar si es archivo estático
    if (file_exists(BASE_PATH . '/' . $route)) {
        // Servir archivo directamente
        $extension = pathinfo($route, PATHINFO_EXTENSION);
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
        ];
        
        if (isset($mime_types[$extension])) {
            header('Content-Type: ' . $mime_types[$extension]);
        }
        
        readfile(BASE_PATH . '/' . $route);
        exit;
    }
    
    http_response_code(404);
    echo "<h1>404 - Página no encontrada</h1>";
    echo "<p>Ruta: $route</p>";
}
?>