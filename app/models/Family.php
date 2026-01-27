<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    protected $table = 'families';
    
    protected $fillable = ['code', 'name', 'segment_id', 'description'];
    
    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class);
    }
    
    public function classes(): HasMany
    {
        return $this->hasMany(ClassModel::class);
    }
}