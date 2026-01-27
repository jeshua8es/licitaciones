<?php
// api/ofertas.php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Oferta;

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'delete':
        $id = $_GET['id'] ?? 0;
        
        $oferta = Oferta::find($id);
        if ($oferta && $oferta->delete()) {
            echo json_encode(['success' => true, 'message' => 'Oferta eliminada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>