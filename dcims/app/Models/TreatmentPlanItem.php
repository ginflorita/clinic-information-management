<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentPlanItem extends Model
{
    use HasFactory;

    const STATUSES = ['proposed', 'accepted', 'declined', 'completed'];

    protected $fillable = [
        'treatment_plan_id',
        'procedure_id',
        'tooth_id',
        'status',
        'quantity',
        'estimated_unit_price',
        'estimated_total',
        'priority',
        'notes',
    ];

    protected $casts = [
        'estimated_unit_price' => 'decimal:2',
        'estimated_total' => 'decimal:2',
    ];

    public function treatmentPlan(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlan::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }
}
