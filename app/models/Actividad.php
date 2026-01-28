<?php
// app/models/Actividad.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'codigo_segmento',
        'segmento',
        'codigo_familia',
        'familia',
        'codigo_clase',
        'clase',
        'codigo_producto',
        'producto'
    ];

    // Relación con Oferta
    public function ofertas()
    {
        return $this->hasMany(Oferta::class, 'actividad_id');
    }

    public function getAll()
    {
        $query = $this->db->query("
        SELECT * FROM actividades 
        ORDER BY codigo_segmento, codigo_familia, codigo_clase, codigo_producto
    ");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimaFecha()
    {
        $query = $this->db->query("
        SELECT MAX(creado_en) as ultima FROM actividades
    ");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['ultima'] ?? null;
    }
}
