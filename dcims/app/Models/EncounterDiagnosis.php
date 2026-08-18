<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncounterDiagnosis extends Model
{
    use HasFactory;

    const STATUSES = ['suspected', 'active', 'resolved', 'historical'];

    protected $fillable = [
        'encounter_id',
        'patient_id',
        'diagnosis_id',
        'tooth_id',
        'status',
        'notes',
        'diagnosed_by',
        'diagnosed_at',
    ];

    protected $casts = [
        'diagnosed_at' => 'datetime',
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }

    public function diagnoser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diagnosed_by');
    }
}
