<?php
// app/controllers/ProductController.php
require_once 'Controller.php';

class ProductController extends Controller
{
    public function index()
    {
        try {
            echo json_encode([
                'success' => true,
                'message' => 'ProductController funcionando',
                'data' => [
                    ['id' => 1, 'codigo_producto' => '001', 'producto' => 'Producto 1'],
                    ['id' => 2, 'codigo_producto' => '002', 'producto' => 'Producto 2']
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}