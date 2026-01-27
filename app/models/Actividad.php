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
}