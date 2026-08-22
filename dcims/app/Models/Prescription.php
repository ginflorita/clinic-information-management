<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class Prescription extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['active', 'cancelled'];

    protected $fillable = [
        'prescription_number',
        'patient_id',
        'encounter_id',
        'provider_id',
        'status',
        'prescribed_at',
        'notes',
    ];

    protected $casts = [
        'prescribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Prescription $prescription) {
            if (empty($prescription->prescription_number)) {
                $prescription->prescription_number = static::generatePrescriptionNumber();
            }
        });
    }

    public static function generatePrescriptionNumber(): string
    {
        $prefix = 'RX-'.now()->year.'-';

        $last = static::where('prescription_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('prescription_number')
            ->value('prescription_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function cancel(): void
    {
        if ($this->status !== 'active') {
            throw new LogicException("Cannot cancel a {$this->status} prescription.");
        }

        $this->update(['status' => 'cancelled']);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
