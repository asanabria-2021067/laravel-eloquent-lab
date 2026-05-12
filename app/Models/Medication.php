<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'dosage_form',
        'stock',
        'reorder_level',
        'price',
        'is_prescription_only',
    ];

    protected $casts = [
        'stock' => 'integer',
        'reorder_level' => 'integer',
        'price' => 'decimal:2',
        'is_prescription_only' => 'boolean',
    ];

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class)
            ->withPivot(['dosage_amount', 'dosage_unit', 'instructions', 'administered_at'])
            ->withTimestamps();
    }
}
