<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    use Auditable, HasFactory;

    public const ACTIVE_STATUSES_EXCLUDED_FROM_CONFLICTS = ['cancelled', 'no_show'];

    protected $fillable = [
        'appointment_number',
        'patient_id',
        'provider_id',
        'appointment_type_id',
        'chair_id',
        'scheduled_start',
        'scheduled_end',
        'status',
        'reason',
        'notes',
        'cancelled_at',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment) {
            if (empty($appointment->appointment_number)) {
                $appointment->appointment_number = static::generateAppointmentNumber();
            }
        });
    }

    public static function generateAppointmentNumber(): string
    {
        $prefix = 'APT-'.now()->year.'-';

        $last = static::where('appointment_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('appointment_number')
            ->value('appointment_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Mirrors the DB-level EXCLUDE USING gist constraints (Postgres-only) so the
     * same double-booking rule holds under SQLite tests and gives immediate
     * validation feedback instead of a raw constraint-violation exception.
     */
    public static function hasConflict(int $providerId, ?int $chairId, string $start, string $end, ?int $excludeId = null): bool
    {
        // Normalize to a consistent "Y-m-d H:i:s" string before comparing —
        // callers may pass HTML datetime-local values with no seconds, which
        // otherwise compare incorrectly against full-precision stored values
        // under SQLite's lexicographic string comparison.
        $start = Carbon::parse($start)->format('Y-m-d H:i:s');
        $end = Carbon::parse($end)->format('Y-m-d H:i:s');

        $overlaps = fn ($query) => $query
            ->whereNotIn('status', self::ACTIVE_STATUSES_EXCLUDED_FROM_CONFLICTS)
            ->where('scheduled_start', '<', $end)
            ->where('scheduled_end', '>', $start)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId));

        if ($overlaps(static::where('provider_id', $providerId))->exists()) {
            return true;
        }

        if ($chairId && $overlaps(static::where('chair_id', $chairId))->exists()) {
            return true;
        }

        return false;
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    public function encounter(): HasOne
    {
        return $this->hasOne(Encounter::class);
    }

    public function chair(): BelongsTo
    {
        return $this->belongsTo(Chair::class);
    }
}
