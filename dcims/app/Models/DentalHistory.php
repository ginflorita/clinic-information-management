<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DentalHistory extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'patient_id',
        'previous_dentist',
        'previous_treatments',
        'previous_extraction',
        'previous_root_canal',
        'prosthetic_history',
        'orthodontic_history',
        'previous_surgery',
        'previous_complications',
        'dental_habits',
        'oral_hygiene',
        'chief_concerns',
        'recorded_at',
        'recorded_by_user_id',
    ];

    protected $casts = [
        'previous_extraction' => 'boolean',
        'previous_root_canal' => 'boolean',
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
