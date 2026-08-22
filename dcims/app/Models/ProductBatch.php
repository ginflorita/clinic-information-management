<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBatch extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'batch_number',
        'lot_number',
        'expiry_date',
        'quantity',
        'unit_cost',
        'received_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_at' => 'date',
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function isExpiringWithin(int $days): bool
    {
        return $this->expiry_date !== null
            && $this->quantity > 0
            && $this->expiry_date->lessThanOrEqualTo(today()->addDays($days));
    }

    public function scopeExpiringWithin(Builder $query, int $days): Builder
    {
        return $query->where('quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', today()->addDays($days));
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'batch_id');
    }
}
