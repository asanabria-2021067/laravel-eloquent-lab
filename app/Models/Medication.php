<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'presentation',
        'description',
        'stock',
        'reorder_level',
        'unit_cost',
    ];

    protected $casts = [
        'stock' => 'integer',
        'reorder_level' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
