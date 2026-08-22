<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerioExamination extends Model
{
    use Auditable, HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'patient_id',
        'encounter_id',
        'examined_at',
        'examined_by',
    ];

    protected $casts = [
        'examined_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examined_by');
    }

    public function toothRecords(): HasMany
    {
        return $this->hasMany(PerioToothRecord::class);
    }
}
