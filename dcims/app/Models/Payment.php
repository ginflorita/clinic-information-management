<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class Payment extends Model
{
    use HasFactory;

    const STATUSES = ['completed', 'voided', 'refunded'];

    protected $fillable = [
        'payment_number',
        'patient_id',
        'invoice_id',
        'payment_date',
        'payment_method_id',
        'amount',
        'reference_number',
        'status',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = static::generatePaymentNumber();
            }
        });
    }

    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY-'.now()->year.'-';

        $last = static::where('payment_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('payment_number')
            ->value('payment_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function void(): void
    {
        if ($this->status !== 'completed') {
            throw new LogicException('Only a completed payment can be voided.');
        }

        $this->update(['status' => 'voided']);
    }

    public function refund(): void
    {
        if ($this->status !== 'completed') {
            throw new LogicException('Only a completed payment can be refunded.');
        }

        $this->update(['status' => 'refunded']);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
