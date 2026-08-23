<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class LabOrder extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['pending', 'sent', 'in_progress', 'ready', 'received', 'cancelled'];

    const TRANSITIONS = [
        'pending' => ['sent', 'cancelled'],
        'sent' => ['in_progress', 'cancelled'],
        'in_progress' => ['ready', 'cancelled'],
        'ready' => ['received', 'cancelled'],
    ];

    protected $fillable = [
        'case_number',
        'patient_id',
        'lab_id',
        'procedure_id',
        'tooth_id',
        'sent_date',
        'expected_date',
        'received_date',
        'status',
        'cost',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'sent_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'cost' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (LabOrder $order) {
            if (empty($order->case_number)) {
                $order->case_number = static::generateCaseNumber();
            }
        });
    }

    public static function generateCaseNumber(): string
    {
        $prefix = 'LAB-'.now()->year.'-';

        $last = static::where('case_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('case_number')
            ->value('case_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function availableTransitions(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::TRANSITIONS[$this->status] ?? [], true)) {
            throw new LogicException("Cannot transition a {$this->status} lab order to {$status}.");
        }

        $updates = ['status' => $status];

        if ($status === 'sent' && $this->sent_date === null) {
            $updates['sent_date'] = now()->toDateString();
        }
        if ($status === 'received' && $this->received_date === null) {
            $updates['received_date'] = now()->toDateString();
        }

        $this->update($updates);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function tooth(): BelongsTo
    {
        return $this->belongsTo(Tooth::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
