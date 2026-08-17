<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'queue_date',
        'queue_number',
        'status',
        'checked_in_at',
        'called_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'checked_in_at' => 'datetime',
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public static function nextQueueNumberFor(string $date): int
    {
        // whereDate() rather than a plain equality check — the `date` cast
        // doesn't guarantee the stored value has no time component in every
        // driver, so a raw string comparison can miss same-day rows.
        $last = static::whereDate('queue_date', $date)->max('queue_number');

        return $last ? $last + 1 : 1;
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
