<?php
// app/controllers/FamilyController.php
require_once 'Controller.php';

class FamilyController extends Controller
{
    public function index()
    {
        try {
            echo json_encode([
                'success' => true,
                'message' => 'FamilyController funcionando',
                'data' => [
                    ['id' => 1, 'codigo_familia' => '001', 'familia' => 'Familia 1'],
                    ['id' => 2, 'codigo_familia' => '002', 'familia' => 'Familia 2']
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}