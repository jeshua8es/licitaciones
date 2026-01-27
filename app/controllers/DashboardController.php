<?php
namespace App\Controllers;

use App\Models\Oferta;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Obtener estadísticas usando Eloquent
            $total_ofertas = Oferta::count();
            $activas = Oferta::where('estado', 'activa')->count();
            $pendientes = Oferta::where('estado', 'pendiente')->count();
            $cerradas = Oferta::where('estado', 'cerrada')->count();
            
            // Obtener últimas ofertas
            $ultimas_ofertas = Oferta::orderBy('creado_en', 'desc')
                                     ->take(5)
                                     ->get()
                                     ->toArray();
            
        } catch (Exception $e) {
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
        }
        
        // Pasar datos a la vista
        $data = [
            'total_ofertas' => $total_ofertas,
            'activas' => $activas,
            'pendientes' => $pendientes,
            'cerradas' => $cerradas,
            'ultimas_ofertas' => $ultimas_ofertas,
            'BASE_URL' => BASE_URL
        ];
        
        $this->view('dashboard/index', $data);
    }
}
?>