<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

class ProcedureRecord extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['completed', 'voided'];

    protected $fillable = [
        'encounter_id',
        'procedure_id',
        'patient_id',
        'provider_id',
        'tooth_id',
        'treatment_plan_item_id',
        'status',
        'quantity',
        'unit_price',
        'total_amount',
        'performed_at',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'performed_at' => 'datetime',
    ];

    public function void(): void
    {
        if ($this->status !== 'completed') {
            throw new LogicException('Only a completed procedure record can be voided.');
        }

        $this->update(['status' => 'voided']);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }

    public function treatmentPlanItem(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanItem::class);
    }

    public function invoiceItem(): HasOne
    {
        return $this->hasOne(InvoiceItem::class);
    }
}
