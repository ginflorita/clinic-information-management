<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OdontogramEntry extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'odontogram_id',
        'tooth_id',
        'condition_id',
        'status',
        'notes',
    ];

    public function odontogram(): BelongsTo
    {
        return $this->belongsTo(Odontogram::class);
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(ToothCondition::class, 'condition_id');
    }

    public function surfaces(): HasMany
    {
        return $this->hasMany(OdontogramEntrySurface::class);
    }
}
