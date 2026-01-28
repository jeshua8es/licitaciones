<?php
namespace App\Controllers;

use App\Models\Oferta;
use App\Models\Actividad;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Obtener estadísticas de ofertas usando Eloquent
            $total_ofertas = Oferta::count();
            $activas = Oferta::where('estado', 'activa')->count();
            $pendientes = Oferta::where('estado', 'pendiente')->count();
            $cerradas = Oferta::where('estado', 'cerrada')->count();
            
            // Obtener últimas ofertas
            $ultimas_ofertas = Oferta::orderBy('creado_en', 'desc')
                                     ->take(5)
                                     ->get()
                                     ->toArray();
            
            // Obtener estadísticas de actividades UNSPSC
            $total_actividades = Actividad::count();
            $ultimas_actividades = Actividad::orderBy('id', 'desc')
                                           ->take(3)
                                           ->get()
                                           ->toArray();
            
            // Obtener fecha de última importación
            $ultima_importacion = Actividad::orderBy('id', 'desc')
                                          ->first();
            $fecha_ultima_importacion = $ultima_importacion 
                ? date('d/m/Y H:i', strtotime($ultima_importacion->creado_en)) 
                : 'Nunca';
            
        } catch (\Exception $e) {
            // Si hay error en la base de datos, usar datos de ejemplo
            $total_ofertas = 3;
            $activas = 1;
            $pendientes = 1;
            $cerradas = 1;
            
            $ultimas_ofertas = [
                ['id' => 1, 'consecutivo' => 'O-0001-24', 'objeto' => 'Oferta Demo 1', 'estado' => 'activa', 'creado_en' => '2024-01-15'],
                ['id' => 2, 'consecutivo' => 'O-0002-24', 'objeto' => 'Oferta Demo 2', 'estado' => 'pendiente', 'creado_en' => '2024-01-14'],
                ['id' => 3, 'consecutivo' => 'O-0003-24', 'objeto' => 'Oferta Demo 3', 'estado' => 'cerrada', 'creado_en' => '2024-01-13'],
            ];
            
            // Datos de ejemplo para actividades
            $total_actividades = 0;
            $ultimas_actividades = [];
            $fecha_ultima_importacion = 'Nunca';
        }
        
        // Pasar datos a la vista
        $data = [
            // Estadísticas de ofertas
            'total_ofertas' => $total_ofertas,
            'activas' => $activas,
            'pendientes' => $pendientes,
            'cerradas' => $cerradas,
            'ultimas_ofertas' => $ultimas_ofertas,
            
            // Estadísticas de actividades UNSPSC
            'total_actividades' => $total_actividades,
            'ultimas_actividades' => $ultimas_actividades,
            'fecha_ultima_importacion' => $fecha_ultima_importacion,
            
            // Configuración
            'BASE_URL' => BASE_URL
        ];
        
        $this->view('dashboard/index', $data);
    }
}
?>