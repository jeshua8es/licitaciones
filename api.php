<?php
// api.php - CON CRUD COMPLETO
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

// SIMULAR BASE DE DATOS EN MEMORIA
session_start();
if (!isset($_SESSION['ofertas'])) {
    $_SESSION['ofertas'] = [
        ['id' => 1, 'titulo' => 'Oferta Demo 1', 'descripcion' => 'Descripción 1', 'estado' => 'activa', 'fecha_limite' => '2024-12-31', 'presupuesto' => 50000, 'created_at' => '2024-01-15'],
        ['id' => 2, 'titulo' => 'Oferta Demo 2', 'descripcion' => 'Descripción 2', 'estado' => 'pendiente', 'fecha_limite' => '2024-11-30', 'presupuesto' => 30000, 'created_at' => '2024-01-14'],
        ['id' => 3, 'titulo' => 'Oferta Demo 3', 'descripcion' => 'Descripción 3', 'estado' => 'cerrada', 'fecha_limite' => '2024-10-15', 'presupuesto' => 75000, 'created_at' => '2024-01-13']
    ];
}

$ofertas = &$_SESSION['ofertas'];

// ENDPOINTS
if ($url === 'ofertas') {
    if ($method === 'GET') {
        // LISTAR
        echo json_encode($ofertas);
        exit;
    }
    
    if ($method === 'POST') {
        // CREAR
        $input = json_decode(file_get_contents('php://input'), true);
        $nuevaOferta = [
            'id' => count($ofertas) + 1,
            'titulo' => $input['titulo'] ?? 'Nueva Oferta',
            'descripcion' => $input['descripcion'] ?? '',
            'estado' => $input['estado'] ?? 'pendiente',
            'fecha_limite' => $input['fecha_limite'] ?? null,
            'presupuesto' => $input['presupuesto'] ?? 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $ofertas[] = $nuevaOferta;
        echo json_encode(['success' => true, 'id' => $nuevaOferta['id'], 'oferta' => $nuevaOferta]);
        exit;
    }
}

// CRUD por ID: /ofertas/{id}
if (preg_match('/^ofertas\/(\d+)$/', $url, $matches)) {
    $id = (int)$matches[1];
    
    if ($method === 'GET') {
        // VER
        foreach ($ofertas as $oferta) {
            if ($oferta['id'] === $id) {
                echo json_encode($oferta);
                exit;
            }
        }
        echo json_encode(['error' => 'Oferta no encontrada']);
        exit;
    }
    
    if ($method === 'PUT') {
        // ACTUALIZAR
        $input = json_decode(file_get_contents('php://input'), true);
        foreach ($ofertas as &$oferta) {
            if ($oferta['id'] === $id) {
                $oferta = array_merge($oferta, $input);
                $oferta['updated_at'] = date('Y-m-d H:i:s');
                echo json_encode(['success' => true, 'oferta' => $oferta]);
                exit;
            }
        }
        echo json_encode(['error' => 'Oferta no encontrada']);
        exit;
    }
    
    if ($method === 'DELETE') {
        // ELIMINAR
        foreach ($ofertas as $key => $oferta) {
            if ($oferta['id'] === $id) {
                array_splice($ofertas, $key, 1);
                echo json_encode(['success' => true, 'message' => 'Oferta eliminada']);
                exit;
            }
        }
        echo json_encode(['error' => 'Oferta no encontrada']);
        exit;
    }
}

// ENDPOINTS PARA CATÁLOGOS
$catalogos = [
    'segments' => [['id' => 1, 'nombre' => 'Tecnología'], ['id' => 2, 'nombre' => 'Oficina']],
    'families' => [['id' => 1, 'segment_id' => 1, 'nombre' => 'Hardware'], ['id' => 2, 'segment_id' => 1, 'nombre' => 'Software']],
    'classes' => [['id' => 1, 'family_id' => 1, 'nombre' => 'Computadoras'], ['id' => 2, 'family_id' => 1, 'nombre' => 'Periféricos']],
    'products' => [['id' => 1, 'class_id' => 1, 'nombre' => 'Laptop Dell', 'codigo' => 'PROD001'], ['id' => 2, 'class_id' => 1, 'nombre' => 'PC Desktop', 'codigo' => 'PROD002']]
];

if (isset($catalogos[$url]) && $method === 'GET') {
    echo json_encode($catalogos[$url]);
    exit;
}

// IMPORTACIÓN
if ($url === 'importar' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $registros = $input['registros'] ?? [];
    $tipo = $input['tipo'] ?? 'ofertas';
    
    $resultado = [
        'success' => true,
        'message' => 'Importación simulada exitosa',
        'tipo' => $tipo,
        'total' => count($registros),
        'importados' => count($registros),
        'errores' => []
    ];
    
    // Simular procesamiento
    foreach ($registros as $index => $registro) {
        if ($tipo === 'ofertas') {
            $nuevaOferta = [
                'id' => count($ofertas) + 1,
                'titulo' => $registro['titulo'] ?? 'Importado',
                'descripcion' => $registro['descripcion'] ?? '',
                'estado' => $registro['estado'] ?? 'pendiente',
                'fecha_limite' => $registro['fecha_limite'] ?? null,
                'presupuesto' => $registro['presupuesto'] ?? 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $ofertas[] = $nuevaOferta;
        }
    }
    
    echo json_encode($resultado);
    exit;
}

echo json_encode(['api' => 'Sistema de Licitaciones', 'version' => '2.0', 'status' => 'active']);
?>