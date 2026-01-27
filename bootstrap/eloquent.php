<?php
// bootstrap/eloquent.php - SILENCIAR WARNINGS

// 1. SILENCIAR ERRORES DEPRECATED ANTES DE CUALQUIER COSA
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

// 2. Ruta absoluta
define('ROOT', dirname(__DIR__));

// 3. Cargar Composer
require_once ROOT . '/vendor/autoload.php';

// 4. Configurar Eloquent
$capsule = new Illuminate\Database\Capsule\Manager;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'port'      => 3307,
    'database'  => 'licitaciones',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// 5. Configurar timezone
date_default_timezone_set('America/Bogota');