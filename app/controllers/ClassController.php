<?php
// app/controllers/ClassController.php
require_once 'Controller.php';

class ClassController extends Controller
{
    public function index()
    {
        try {
            echo json_encode([
                'success' => true,
                'message' => 'ClassController funcionando',
                'data' => [
                    ['id' => 1, 'codigo_clase' => '001', 'clase' => 'Clase 1'],
                    ['id' => 2, 'codigo_clase' => '002', 'clase' => 'Clase 2']
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}