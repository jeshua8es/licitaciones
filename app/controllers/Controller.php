<?php
namespace App\Controllers;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        
        // Buscar la vista
        $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("Vista no encontrada: $viewFile");
        }
    }
    
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
    
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>