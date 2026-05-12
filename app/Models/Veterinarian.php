<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veterinarian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty',
        'license_number',
        'years_experience',
        'biography',
        'available_from',
        'available_to',
        'consultation_fee',
    ];

    protected $casts = [
        'available_from' => 'datetime:H:i',
        'available_to' => 'datetime:H:i',
        'consultation_fee' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
