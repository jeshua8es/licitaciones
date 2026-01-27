<?php
// bootstrap/autoload.php

// 1. Cargar Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Configurar Eloquent
require_once __DIR__ . '/eloquent.php';

// 3. Autoloader manual para nuestras clases
spl_autoload_register(function ($className) {
    // Define los namespaces y sus directorios
    $namespaces = [
        'App\\Controllers\\' => __DIR__ . '/../app/controllers/',
        'App\\Models\\' => __DIR__ . '/../app/models/',
    ];
    
    foreach ($namespaces as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) === 0) {
            $relativeClass = substr($className, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// 4. Función para debug del autoload
function debugAutoload($className) {
    error_log("Intentando cargar: $className");
}
// spl_autoload_register('debugAutoload'); // Descomentar para debug