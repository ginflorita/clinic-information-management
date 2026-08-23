<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class Referral extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['draft', 'sent', 'received', 'completed', 'cancelled'];

    const TRANSITIONS = [
        'draft' => ['sent', 'cancelled'],
        'sent' => ['received', 'cancelled'],
        'received' => ['completed'],
    ];

    protected $fillable = [
        'referral_number',
        'patient_id',
        'referring_provider_id',
        'receiving_name',
        'receiving_specialty',
        'receiving_contact',
        'reason',
        'clinical_summary',
        'referral_date',
        'status',
        'response',
        'created_by',
    ];

    protected $casts = [
        'referral_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Referral $referral) {
            if (empty($referral->referral_number)) {
                $referral->referral_number = static::generateReferralNumber();
            }
        });
    }

    public static function generateReferralNumber(): string
    {
        $prefix = 'REF-'.now()->year.'-';

        $last = static::where('referral_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('referral_number')
            ->value('referral_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function availableTransitions(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    public function transitionTo(string $status, ?string $response = null): void
    {
        if (! in_array($status, self::TRANSITIONS[$this->status] ?? [], true)) {
            throw new LogicException("Cannot transition a {$this->status} referral to {$status}.");
        }

        $this->update(array_filter([
            'status' => $status,
            'response' => $response,
        ], fn ($value) => $value !== null));
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function referringProvider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'referring_provider_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
