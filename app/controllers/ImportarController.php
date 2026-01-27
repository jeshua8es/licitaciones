<?php
namespace App\Controllers;

use App\Models\Actividad;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportarController extends Controller
{
    public function index()
    {
        $this->view('importar/index', ['BASE_URL' => BASE_URL]);
    }
    
    public function procesar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
            try {
                $file = $_FILES['archivo']['tmp_name'];
                
                // Cargar Excel
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
                
                // Saltar encabezado (fila 1)
                array_shift($rows);
                
                $importados = 0;
                $errores = 0;
                
                foreach ($rows as $row) {
                    if (count($row) >= 8) {
                        try {
                            $actividad = new Actividad();
                            $actividad->codigo_segmento = $row[0] ?? null;
                            $actividad->segmento = $row[1] ?? '';
                            $actividad->codigo_familia = $row[2] ?? null;
                            $actividad->familia = $row[3] ?? '';
                            $actividad->codigo_clase = $row[4] ?? null;
                            $actividad->clase = $row[5] ?? '';
                            $actividad->codigo_producto = $row[6] ?? null;
                            $actividad->producto = $row[7] ?? '';
                            
                            if ($actividad->save()) {
                                $importados++;
                            }
                        } catch (\Exception $e) {
                            $errores++;
                        }
                    }
                }
                
                $_SESSION['import_result'] = [
                    'success' => true,
                    'importados' => $importados,
                    'errores' => $errores
                ];
                
            } catch (\Exception $e) {
                $_SESSION['import_result'] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        $this->redirect(BASE_URL . '/importar');
    }
}
?>