<?php
// app/controllers/SegmentController.php
require_once 'Controller.php';

class SegmentController extends Controller
{
    public function index()
    {
        try {
            require_once __DIR__ . '/../models/Segment.php';
            
            $segments = Segment::select(['id', 'codigo_segmento', 'segmento'])
                ->orderBy('codigo_segmento')
                ->get();
            
            echo json_encode([
                'success' => true,
                'data' => $segments,
                'count' => $segments->count()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}