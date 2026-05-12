<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'veterinarian_id',
        'service_id',
        'scheduled_at',
        'status',
        'follow_up_required',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'follow_up_required' => 'boolean',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(Veterinarian::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function medications(): BelongsToMany
    {
        return $this->belongsToMany(Medication::class)
            ->withPivot(['dosage_amount', 'dosage_unit', 'instructions', 'administered_at'])
            ->withTimestamps();
    }
}
