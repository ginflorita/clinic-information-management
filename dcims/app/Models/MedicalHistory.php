<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'patient_id',
        'previous_surgeries',
        'hospitalization',
        'current_medications',
        'pregnancy_status',
        'smoking_status',
        'alcohol_use',
        'family_medical_history',
        'physician_name',
        'physician_contact',
        'medical_alerts',
        'recorded_at',
        'recorded_by_user_id',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
