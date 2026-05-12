<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'medication_id',
        'dosage_amount',
        'dosage_unit',
        'frequency',
        'duration_days',
        'notes',
    ];

    protected $casts = [
        'dosage_amount' => 'decimal:2',
        'duration_days' => 'integer',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
