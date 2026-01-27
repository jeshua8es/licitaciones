<?php
echo "<h1>Listado de Archivos</h1>";

$base_dir = __DIR__;
$public_dir = $base_dir . '/public';

echo "<p>Directorio base: $base_dir</p>";
echo "<p>Directorio public: $public_dir</p>";

// Verificar si existe la carpeta
if (!is_dir($public_dir)) {
    echo "<p style='color:red'>❌ La carpeta public/ NO existe</p>";
    exit;
}

echo "<p style='color:green'>✅ La carpeta public/ EXISTE</p>";

// Listar TODO
echo "<h2>Contenido de public/:</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Nombre</th><th>Tipo</th><th>Tamaño</th><th>¿Index?</th></tr>";

$files = scandir($public_dir);
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    
    $full_path = $public_dir . '/' . $file;
    $type = is_dir($full_path) ? '📁 Carpeta' : '📄 Archivo';
    $size = is_file($full_path) ? filesize($full_path) . ' bytes' : '-';
    
    // Verificar si es un archivo índice
    $is_index = (strtolower($file) == 'index.php' || 
                 strtolower($file) == 'index.html' || 
                 strtolower($file) == 'index.htm') ? '⭐' : '';
    
    echo "<tr>
            <td>$file $is_index</td>
            <td>$type</td>
            <td>$size</td>
            <td>" . ($is_index ? 'SÍ' : '') . "</td>
          </tr>";
}
echo "</table>";

// Buscar específicamente archivos índice
echo "<h2>Buscando archivos índice:</h2>";
$index_files = ['index.php', 'index.html', 'index.htm', 'Index.php', 'INDEX.PHP'];
foreach ($index_files as $index_file) {
    $test_path = $public_dir . '/' . $index_file;
    echo "<p>Probando $index_file: " . 
         (file_exists($test_path) ? 
          "✅ EXISTE (" . filesize($test_path) . " bytes)" : 
          "❌ NO existe") . 
         "</p>";
}

// Mostrar ruta completa de posibles índices
echo "<h2>Rutas completas probadas:</h2>";
foreach ($index_files as $index_file) {
    $test_path = $public_dir . '/' . $index_file;
    echo "<p>$test_path</p>";
}
?>