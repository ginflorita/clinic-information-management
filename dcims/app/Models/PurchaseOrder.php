<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class PurchaseOrder extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['draft', 'ordered', 'partially_received', 'received', 'cancelled'];

    const TRANSITIONS = [
        'draft' => ['ordered', 'cancelled'],
        'ordered' => ['partially_received', 'received', 'cancelled'],
        'partially_received' => ['received', 'cancelled'],
    ];

    protected $fillable = [
        'po_number',
        'supplier_id',
        'status',
        'order_date',
        'expected_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $po) {
            if (empty($po->po_number)) {
                $po->po_number = static::generatePoNumber();
            }
        });
    }

    public static function generatePoNumber(): string
    {
        $prefix = 'PO-'.now()->year.'-';

        $last = static::where('po_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('po_number')
            ->value('po_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::TRANSITIONS[$this->status] ?? [], true)) {
            throw new LogicException("Cannot transition a {$this->status} purchase order to {$status}.");
        }

        if ($status === 'ordered' && $this->items()->count() === 0) {
            throw new LogicException('Add at least one item before marking this purchase order as ordered.');
        }

        $this->update(['status' => $status]);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }
}
