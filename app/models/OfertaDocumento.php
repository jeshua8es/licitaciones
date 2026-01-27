<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertaDocumento extends Model
{
    protected $table = 'ofertas_documentos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'licitacion_id',
        'titulo',
        'descripcion',
        'archivo',
        'creado_en'
    ];
    
    protected $dates = ['creado_en'];
    
    // Relación con Oferta
    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'licitacion_id');
    }
    
    // Accesor para URL completa del archivo
    public function getArchivoUrlAttribute()
    {
        if (!$this->archivo) return null;
        return '/PHP/licitacion/uploads/' . $this->archivo;
    }
    
    // Accesor para tipo de archivo
    public function getTipoArchivoAttribute()
    {
        if (!$this->archivo) return 'desconocido';
        $extension = pathinfo($this->archivo, PATHINFO_EXTENSION);
        return strtoupper($extension);
    }
    
    // Accesor para icono según tipo
    public function getIconoAttribute()
    {
        $extension = pathinfo($this->archivo, PATHINFO_EXTENSION);
        return $extension === 'pdf' ? 'bi-file-earmark-pdf' : 'bi-file-earmark-zip';
    }
}