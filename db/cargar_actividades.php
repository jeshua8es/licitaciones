<?php
// db/cargar_actividades.php
require_once __DIR__ . '/../bootstrap/eloquent.php';

use Illuminate\Database\Capsule\Manager as DB;

// 1. Crear tablas si no existen
$sql = "
CREATE TABLE IF NOT EXISTS segments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_segmento INT NOT NULL,
    segmento VARCHAR(200) NOT NULL,
    UNIQUE KEY unique_codigo_segmento (codigo_segmento)
);

CREATE TABLE IF NOT EXISTS families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_familia INT NOT NULL,
    familia VARCHAR(200) NOT NULL,
    segmento_id INT,
    FOREIGN KEY (segmento_id) REFERENCES segments(id),
    UNIQUE KEY unique_codigo_familia (codigo_familia)
);

CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_clase INT NOT NULL,
    clase VARCHAR(200) NOT NULL,
    familia_id INT,
    FOREIGN KEY (familia_id) REFERENCES families(id),
    UNIQUE KEY unique_codigo_clase (codigo_clase)
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_producto INT NOT NULL,
    producto VARCHAR(200) NOT NULL,
    clase_id INT,
    FOREIGN KEY (clase_id) REFERENCES classes(id),
    UNIQUE KEY unique_codigo_producto (codigo_producto)
);

CREATE TABLE IF NOT EXISTS actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_segmento INT NOT NULL,
    segmento VARCHAR(200) NOT NULL,
    codigo_familia INT NOT NULL,
    familia VARCHAR(200) NOT NULL,
    codigo_clase INT NOT NULL,
    clase VARCHAR(200) NOT NULL,
    codigo_producto INT NOT NULL,
    producto VARCHAR(200) NOT NULL,
    INDEX idx_codigo_segmento (codigo_segmento),
    INDEX idx_codigo_familia (codigo_familia)
);
";

// Ejecutar SQL
try {
    DB::unprepared($sql);
    echo "✅ Tablas creadas/existen<br>";
} catch (Exception $e) {
    echo "❌ Error creando tablas: " . $e->getMessage() . "<br>";
}

// 2. Insertar datos de prueba si las tablas están vacías
$segmentsCount = DB::table('segments')->count();
if ($segmentsCount == 0) {
    $segments = [
        ['codigo_segmento' => 10, 'segmento' => 'Servicios'],
        ['codigo_segmento' => 20, 'segmento' => 'Bienes Muebles'],
        ['codigo_segmento' => 30, 'segmento' => 'Obras Civiles']
    ];
    
    foreach ($segments as $segment) {
        DB::table('segments')->insert($segment);
    }
    echo "✅ Datos de prueba insertados en segments<br>";
}