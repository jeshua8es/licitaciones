<?php
// test_guardar.php
require_once 'vendor/autoload.php';
require_once 'app/controllers/OfertaController.php';

$controller = new App\Controllers\OfertaController();

// Datos de prueba
$_POST = [
    'objeto' => 'Prueba de sistema',
    'descripcion' => 'Descripción de prueba',
    'moneda' => 'COP',
    'presupuesto' => 1000000,
    'actividad_id' => 1,
    'fecha_inicio' => '2024-01-01',
    'hora_inicio' => '08:00',
    'fecha_cierre' => '2024-12-31',
    'hora_cierre' => '17:00'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

$controller->guardar();
?>