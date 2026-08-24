<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class AppointmentRequest extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['pending', 'confirmed', 'declined'];

    const TIME_PERIODS = ['morning', 'afternoon', 'evening'];

    protected $fillable = [
        'reference_number',
        'patient_id',
        'appointment_type_id',
        'preferred_date',
        'preferred_time_period',
        'reason',
        'contact_phone',
        'contact_email',
        'status',
        'staff_notes',
        'appointment_id',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AppointmentRequest $appointmentRequest) {
            if (empty($appointmentRequest->reference_number)) {
                $appointmentRequest->reference_number = static::generateReferenceNumber();
            }
        });
    }

    public static function generateReferenceNumber(): string
    {
        $prefix = 'AR-'.now()->year.'-';

        $last = static::where('reference_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('reference_number')
            ->value('reference_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function decline(User $reviewer, ?string $staffNotes = null): void
    {
        if ($this->status !== 'pending') {
            throw new LogicException("Cannot decline a {$this->status} appointment request.");
        }

        $this->update([
            'status' => 'declined',
            'staff_notes' => $staffNotes,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    public function confirm(Appointment $appointment, User $reviewer): void
    {
        if ($this->status !== 'pending') {
            throw new LogicException("Cannot confirm a {$this->status} appointment request.");
        }

        $this->update([
            'status' => 'confirmed',
            'appointment_id' => $appointment->id,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointmentType(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
