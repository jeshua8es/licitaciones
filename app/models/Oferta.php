<?php
// app/models/Oferta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    protected $table = 'ofertas';
    protected $primaryKey = 'id';
    public $timestamps = false; // Temporalmente deshabilitado
    
    protected $fillable = [
        'consecutivo',
        'objeto',
        'descripcion'
    ];
    
    // Temporalmente sin relaciones para test
}