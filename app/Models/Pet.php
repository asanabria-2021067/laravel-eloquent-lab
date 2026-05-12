<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'species',
        'breed',
        'sex',
        'birth_date',
        'weight_kg',
        'microchip_code',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight_kg' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
