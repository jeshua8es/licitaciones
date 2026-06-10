<?php
// routes/api.php - ACTUALIZADO para manejar CRUD completo

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// RUTAS BASE
$base_dir = dirname(__DIR__);
$controllers_dir = $base_dir . '/app/controllers/';

// VERIFICAR DIRECTORIO
if (!is_dir($controllers_dir)) {
    http_response_code(500);
    echo json_encode(['error' => 'Directorio de controladores no encontrado']);
    exit;
}

// OBTENER ENDPOINT
$url = $_GET['url'] ?? '';
$url = trim($url, '/');
$parts = explode('/', $url);
$endpoint = $parts[0] ?? '';
$id = $parts[1] ?? null;
$method = strtolower($_SERVER['REQUEST_METHOD']);

$eloquent_path = $base_dir . '/bootstrap/eloquent.php';
if (file_exists($eloquent_path)) {
    require_once $eloquent_path;
}

$resolveControllerClass = function ($controllerName) {
    if (class_exists($controllerName)) {
        return $controllerName;
    }

    $namespaced = 'App\\Controllers\\' . $controllerName;
    if (class_exists($namespaced)) {
        return $namespaced;
    }

    return null;
};

// SI NO HAY ENDPOINT, MOSTRAR DOCUMENTACIÓN
if (empty($endpoint)) {
    echo json_encode([
        'api' => 'Sistema de Licitaciones - API',
        'version' => '1.0',
        'endpoints' => [
            'GET    /segments' => 'Listar segmentos',
            'GET    /families' => 'Listar familias',
            'GET    /classes' => 'Listar clases',
            'GET    /products' => 'Listar productos',
            'GET    /actividades' => 'Listar actividades UNSPSC',
            'GET    /ofertas' => 'Listar ofertas (con filtros)',
            'GET    /ofertas/{id}' => 'Ver detalle oferta',
            'POST   /ofertas' => 'Crear oferta',
            'PUT    /ofertas/{id}' => 'Actualizar oferta',
            'DELETE /ofertas/{id}' => 'Eliminar oferta'
        ]
    ]);
    exit;
}

// MAPEO DE CONTROLADORES
$controllers = [
    'segments' => 'SegmentController',
    'families' => 'FamilyController',
    'classes' => 'ClassController',
    'products' => 'ProductController',
    'actividades' => 'ActividadController',
    'ofertas' => 'OfertaController'
];

if ($endpoint === 'ofertas' && $method === 'get') {
    try {
        if ($id !== null) {
            $oferta = \App\Models\Oferta::find($id);

            if (!$oferta) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Oferta no encontrada'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => $oferta
                ]);
            }
        } else {
            $ofertas = \App\Models\Oferta::orderBy('creado_en', 'desc')->get();

            echo json_encode([
                'success' => true,
                'data' => $ofertas,
                'count' => $ofertas->count()
            ]);
        }
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error al consultar ofertas',
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

// ... después del mapeo de controladores, antes de instanciar

// MANEJAR RUTAS ESPECIALES PARA DOCUMENTOS
if ($endpoint === 'documentos') {
    $controller_name = 'OfertaDocumentoController';
    $controller_file = $controllers_dir . $controller_name . '.php';
    
    if (!file_exists($controller_file)) {
        http_response_code(500);
        echo json_encode(['error' => "Controlador '$controller_name' no encontrado"]);
        exit;
    }
    
    require_once $controller_file;
}

// También manejar /ofertas/{id}/documentos
if ($endpoint === 'ofertas' && isset($parts[2]) && $parts[2] === 'documentos') {
    $controller_name = 'OfertaDocumentoController';
    $controller_file = $controllers_dir . $controller_name . '.php';
    
    if (!file_exists($controller_file)) {
        http_response_code(500);
        echo json_encode(['error' => "Controlador '$controller_name' no encontrado"]);
        exit;
    }
    
    require_once $controller_file;
    
    $controllerClass = $resolveControllerClass($controller_name);
    if ($controllerClass === null) {
        http_response_code(500);
        echo json_encode(['error' => "Clase '$controller_name' no definida"]);
        exit;
    }

    $controller = new $controllerClass();
    $oferta_id = $parts[1];
    
    if ($method === 'get') {
        $controller->index($oferta_id);
    } elseif ($method === 'post') {
        $controller->store($oferta_id);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
    }
    exit;
}

if (!isset($controllers[$endpoint])) {
    http_response_code(404);
    echo json_encode(['error' => "Endpoint '$endpoint' no existe"]);
    exit;
}

$controller_name = $controllers[$endpoint];
$controller_file = $controllers_dir . $controller_name . '.php';
$base_controller = $controllers_dir . 'Controller.php';

// VERIFICAR ARCHIVOS
if (!file_exists($controller_file)) {
    http_response_code(500);
    echo json_encode(['error' => "Controlador '$controller_name' no encontrado"]);
    exit;
}

// CARGAR CONTROLADORES
if (file_exists($base_controller)) {
    require_once $base_controller;
}
require_once $controller_file;

// VERIFICAR CLASE
if (!$resolveControllerClass($controller_name)) {
    http_response_code(500);
    echo json_encode(['error' => "Clase '$controller_name' no definida"]);
    exit;
}

// INSTANCIAR CONTROLADOR
$controllerClass = $resolveControllerClass($controller_name);
$controller = new $controllerClass();

if ($endpoint === 'ofertas') {
    // Para el controlador de ofertas, manejar todos los métodos CRUD
    if ($id !== null) {
        switch ($method) {
            case 'get':
                if (method_exists($controller, 'show')) {
                    $controller->show($id);
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método show no disponible']);
                }
                break;
                
            case 'put':
            case 'post': // Algunos clients usan POST para actualizar
                if (method_exists($controller, 'update')) {
                    $controller->update($id);
                } elseif (method_exists($controller, 'actualizar')) {
                    $controller->actualizar($id);
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método update no disponible']);
                }
                break;
                
            case 'delete':
                if (method_exists($controller, 'destroy')) {
                    $controller->destroy($id);
                } elseif (method_exists($controller, 'eliminar')) {
                    $controller->eliminar($id);
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método destroy no disponible']);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    } else {
        // Sin ID - listar o crear
        switch ($method) {
            case 'get':
                if (method_exists($controller, 'index')) {
                    $controller->index();
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método index no disponible']);
                }
                break;
                
            case 'post':
                if (method_exists($controller, 'store')) {
                    $controller->store();
                } elseif (method_exists($controller, 'guardar')) {
                    $controller->guardar();
                } else {
                    http_response_code(405);
                    echo json_encode(['error' => 'Método store no disponible']);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
} else {
    // Para otros controladores, solo GET
    if ($method === 'get') {
        if ($id !== null && method_exists($controller, 'show')) {
            $controller->show($id);
        } elseif (method_exists($controller, 'index')) {
            $controller->index();
        } else {
            echo json_encode([
                'success' => true,
                'message' => "$controller_name funcionando",
                'methods' => get_class_methods($controller)
            ]);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Solo GET permitido para este endpoint']);
    }
}