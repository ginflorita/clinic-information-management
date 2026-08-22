<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerioToothRecord extends Model
{
    use Auditable, HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'perio_examination_id',
        'tooth_id',
        'mobility',
        'furcation',
        'notes',
    ];

    protected $casts = [
        'mobility' => 'integer',
        'furcation' => 'integer',
    ];

    public function perioExamination(): BelongsTo
    {
        return $this->belongsTo(PerioExamination::class);
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(PerioSiteMeasurement::class);
    }
}
