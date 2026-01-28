<?php
// debug_upload.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>DEBUG - Información del archivo:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    echo "<h3>Configuración PHP relevante:</h3>";
    echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
    echo "post_max_size: " . ini_get('post_max_size') . "<br>";
    echo "memory_limit: " . ini_get('memory_limit') . "<br>";
    
    // Probar subida manual
    if (isset($_FILES['archivo'])) {
        $file = $_FILES['archivo'];
        echo "<h4>Validación del archivo:</h4>";
        echo "Nombre: " . $file['name'] . "<br>";
        echo "Tipo: " . $file['type'] . "<br>";
        echo "Tamaño: " . $file['size'] . " bytes<br>";
        echo "Error: " . $file['error'] . "<br>";
        echo "Temp: " . $file['tmp_name'] . "<br>";
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        echo "Extensión: " . $extension . "<br>";
        
        // Verificar si es PDF
        if ($extension === 'pdf') {
            echo "✅ Extensión PDF detectada<br>";
        }
        
        // Verificar tipo MIME
        if ($file['type'] === 'application/pdf') {
            echo "✅ Tipo MIME PDF detectado<br>";
        }
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="archivo">
    <button type="submit">Probar Subida</button>
</form>