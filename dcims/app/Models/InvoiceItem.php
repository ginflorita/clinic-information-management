<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'invoice_id',
        'procedure_id',
        'treatment_plan_item_id',
        'procedure_record_id',
        'description',
        'quantity',
        'unit_price',
        'discount_amount',
        'total_amount',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function treatmentPlanItem(): BelongsTo
    {
        return $this->belongsTo(TreatmentPlanItem::class);
    }

    public function procedureRecord(): BelongsTo
    {
        return $this->belongsTo(ProcedureRecord::class);
    }
}
