<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use Auditable, HasFactory;

    const UPDATED_AT = null;

    const TYPES = ['stock_in', 'stock_out', 'adjustment'];

    protected $fillable = [
        'product_id',
        'batch_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'movement_date',
        'performed_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'movement_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    public function performedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
