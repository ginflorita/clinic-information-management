<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class TreatmentPlan extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['draft', 'presented', 'accepted', 'partially_accepted', 'declined', 'completed', 'expired', 'cancelled'];

    const TRANSITIONS = [
        'draft' => ['presented', 'cancelled'],
        'presented' => ['accepted', 'partially_accepted', 'declined', 'cancelled'],
        'accepted' => ['completed', 'cancelled'],
        'partially_accepted' => ['completed', 'cancelled'],
    ];

    protected $fillable = [
        'patient_id',
        'provider_id',
        'plan_number',
        'title',
        'status',
        'presented_at',
        'accepted_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'presented_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (TreatmentPlan $plan) {
            if (empty($plan->plan_number)) {
                $plan->plan_number = static::generatePlanNumber();
            }
        });
    }

    public static function generatePlanNumber(): string
    {
        $prefix = 'TXP-'.now()->year.'-';

        $last = static::where('plan_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('plan_number')
            ->value('plan_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function transitionTo(string $status): void
    {
        if (! in_array($status, self::TRANSITIONS[$this->status] ?? [], true)) {
            throw new LogicException("Cannot transition a {$this->status} treatment plan to {$status}.");
        }

        $updates = ['status' => $status];

        if ($status === 'presented') {
            $updates['presented_at'] = now();
        } elseif (in_array($status, ['accepted', 'partially_accepted'], true)) {
            $updates['accepted_at'] = now();
        } elseif ($status === 'completed') {
            $updates['completed_at'] = now();
        }

        $this->update($updates);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TreatmentPlanItem::class);
    }
}
