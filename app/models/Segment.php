<?php
// app/models/Segment.php
require_once __DIR__ . '/../../bootstrap/eloquent.php';

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $table = 'segments';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = ['codigo_segmento', 'segmento'];
}