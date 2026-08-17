<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'encounter_number',
        'patient_id',
        'appointment_id',
        'provider_id',
        'encounter_type',
        'status',
        'started_at',
        'ended_at',
        'chief_complaint',
        'clinical_summary',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Encounter $encounter) {
            if (empty($encounter->encounter_number)) {
                $encounter->encounter_number = static::generateEncounterNumber();
            }
        });
    }

    public static function generateEncounterNumber(): string
    {
        $prefix = 'ENC-'.now()->year.'-';

        $last = static::where('encounter_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('encounter_number')
            ->value('encounter_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completed', 'ended_at' => now()]);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function clinicalNotes(): HasMany
    {
        return $this->hasMany(ClinicalNote::class);
    }
}
