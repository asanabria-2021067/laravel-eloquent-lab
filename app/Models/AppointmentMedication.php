<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentMedication extends Model
{
    use HasFactory;

    protected $table = 'appointment_medication';

    protected $fillable = [
        'appointment_id',
        'medication_id',
        'dosage_amount',
        'dosage_unit',
        'instructions',
        'administered_at',
    ];

    protected $casts = [
        'dosage_amount' => 'decimal:2',
        'administered_at' => 'datetime',
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
