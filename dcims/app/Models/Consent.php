<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class Consent extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['granted', 'revoked'];

    protected $fillable = [
        'patient_id',
        'encounter_id',
        'consent_type_id',
        'status',
        'granted_at',
        'revoked_at',
        'obtained_by',
        'notes',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function revoke(): void
    {
        if ($this->status !== 'granted') {
            throw new LogicException("Cannot revoke a {$this->status} consent.");
        }

        $this->update(['status' => 'revoked', 'revoked_at' => now()]);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function consentType(): BelongsTo
    {
        return $this->belongsTo(ConsentType::class);
    }

    public function obtainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'obtained_by');
    }
}
