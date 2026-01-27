<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'code', 
        'name', 
        'description', 
        'class_id',
        'price',
        'unit',
        'specifications'
    ];
    
    protected $casts = [
        'specifications' => 'array',
        'price' => 'decimal:2'
    ];
    
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}