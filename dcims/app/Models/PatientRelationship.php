<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientRelationship extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'patient_id',
        'related_patient_id',
        'contact_name',
        'contact_phone',
        'relationship_type',
        'is_guardian',
        'is_emergency_contact',
    ];

    protected $casts = [
        'is_guardian' => 'boolean',
        'is_emergency_contact' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function relatedPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'related_patient_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->relatedPatient?->full_name ?? $this->contact_name;
    }
}
