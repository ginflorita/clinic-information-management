<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'prescription_id',
        'medication_id',
        'dose',
        'frequency',
        'route',
        'duration',
        'quantity',
        'instructions',
        'refills',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'refills' => 'integer',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
