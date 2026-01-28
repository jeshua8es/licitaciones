<?php
namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'ofertas_documentos';
    protected $fillable = ['licitacion_id', 'titulo', 'descripcion', 'archivo'];
    
    // No usar timestamps de Eloquent, usamos creado_en manual
    public $timestamps = false;
    
    protected $dates = ['creado_en'];
    
    // Relación con oferta
    public function oferta()
    {
        return $this->belongsTo(Oferta::class, 'licitacion_id');
    }
    
    // Guardar automáticamente la fecha
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($documento) {
            $documento->creado_en = date('Y-m-d H:i:s');
        });
    }
}