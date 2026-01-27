<?php
namespace App\Controllers;

use App\Models\Actividad;

class ActividadController 
{
    public function index()
    {
        try {
            // Para select dinámico en frontend
            $actividades = Actividad::select('id', 'producto', 'clase', 'familia', 'segmento')
                                    ->orderBy('producto')
                                    ->get();
            
            echo json_encode([
                'success' => true,
                'data' => $actividades
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener actividades'
            ]);
        }
    }
}