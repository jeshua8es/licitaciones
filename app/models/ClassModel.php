<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    protected $table = 'classes';
    
    protected $fillable = ['code', 'name', 'family_id', 'description'];
    
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }
    
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    // Acceder al segmento a través de la familia
    public function segment()
    {
        return $this->family->segment;
    }
}