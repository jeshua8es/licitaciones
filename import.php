<?php
// import.php - Para procesar archivos CSV/Excel

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../bootstrap/eloquent.php';

use Illuminate\Database\Capsule\Manager as DB;

$response = [
    'success' => false,
    'count' => 0,
    'success_count' => 0,
    'error_count' => 0,
    'errors' => []
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = 'Método no permitido';
    echo json_encode($response);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_OK) {
    $response['error'] = 'No se subió ningún archivo o hubo un error';
    echo json_encode($response);
    exit;
}

$file = $_FILES['file'];
$type = $_POST['type'] ?? 'ofertas';

$allowed_types = ['csv', 'xlsx', 'xls'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($extension, $allowed_types)) {
    $response['error'] = 'Tipo de archivo no permitido. Use CSV, XLSX o XLS';
    echo json_encode($response);
    exit;
}

try {
    if ($extension === 'csv') {
        $result = importCSV($file['tmp_name'], $type);
    } else {
        $result = importExcel($file['tmp_name'], $type);
    }
    
    $response = array_merge($response, $result);
    $response['success'] = true;
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);

function importCSV($filepath, $type) {
    $result = [
        'count' => 0,
        'success_count' => 0,
        'error_count' => 0,
        'errors' => []
    ];
    
    if (($handle = fopen($filepath, 'r')) !== false) {
        $headers = fgetcsv($handle, 1000, ',');
        $line = 1;
        
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $line++;
            $result['count']++;
            
            try {
                $row = array_combine($headers, $data);
                
                switch ($type) {
                    case 'ofertas':
                        importOferta($row);
                        break;
                    case 'productos':
                        importProducto($row);
                        break;
                    case 'catalogos':
                        importCatalogo($row);
                        break;
                }
                
                $result['success_count']++;
                
            } catch (Exception $e) {
                $result['error_count']++;
                $result['errors'][] = [
                    'line' => $line,
                    'message' => $e->getMessage(),
                    'data' => $data
                ];
            }
        }
        
        fclose($handle);
    }
    
    return $result;
}

function importOferta($data) {
    // Mapear y validar datos
    $oferta = [
        'titulo' => $data['titulo'] ?? '',
        'descripcion' => $data['descripcion'] ?? '',
        'estado' => $data['estado'] ?? 'pendiente',
        'fecha_limite' => $data['fecha_limite'] ?? null,
        'presupuesto' => $data['presupuesto'] ?? 0,
        'segment_id' => $data['segment_id'] ?? null,
        'family_id' => $data['family_id'] ?? null,
        'class_id' => $data['class_id'] ?? null,
        'product_id' => $data['product_id'] ?? null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Insertar en la base de datos
    DB::table('ofertas')->insert($oferta);
}

function importProducto($data) {
    $producto = [
        'codigo' => $data['codigo'] ?? '',
        'nombre' => $data['nombre'] ?? '',
        'descripcion' => $data['descripcion'] ?? '',
        'unidad_medida' => $data['unidad_medida'] ?? '',
        'precio_referencial' => $data['precio_referencial'] ?? 0,
        'class_id' => $data['class_id'] ?? null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    DB::table('products')->insert($producto);
}

function importExcel($filepath, $type) {
    // Para Excel necesitarías la librería PhpSpreadsheet
    // Esta es una implementación básica
    return [
        'count' => 0,
        'success_count' => 0,
        'error_count' => 0,
        'errors' => [],
        'message' => 'Importación de Excel requiere PhpSpreadsheet'
    ];
}
?>