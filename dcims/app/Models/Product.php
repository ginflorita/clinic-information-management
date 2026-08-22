<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category_id',
        'unit_id',
        'reorder_level',
        'is_active',
    ];

    protected $casts = [
        'reorder_level' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected function currentStock(): Attribute
    {
        return Attribute::get(fn () => (float) ($this->batches_sum_quantity ?? $this->batches()->sum('quantity')));
    }

    public function isLowStock(): bool
    {
        return $this->reorder_level > 0 && $this->current_stock < $this->reorder_level;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class, 'unit_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
