<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class ClinicalNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'encounter_id',
        'note_type',
        'note_text',
        'status',
        'created_by',
        'signed_by',
        'signed_at',
        'amends_note_id',
        'amendment_reason',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Defense in depth: even if a controller forgets to check status first,
        // the model itself refuses to silently overwrite a signed/amended note's
        // text — the only legitimate path once signed is amend(), which creates
        // a new row instead of mutating this one.
        static::updating(function (ClinicalNote $note) {
            if ($note->isDirty('note_text') && in_array($note->getOriginal('status'), ['signed', 'amended'], true)) {
                // Discard the pending change before throwing, so a caller that
                // catches this and keeps using the same model instance (e.g.
                // to call amend() next) isn't left with a permanently-dirty
                // note_text that would trip this guard again on the next save.
                $note->discardChanges();

                throw new LogicException('A signed clinical note cannot be edited directly — create an amendment instead.');
            }
        });
    }

    public function sign(User $user): void
    {
        if ($this->status !== 'draft') {
            throw new LogicException('Only a draft note can be signed.');
        }

        $this->forceFill([
            'status' => 'signed',
            'signed_by' => $user->id,
            'signed_at' => now(),
        ])->save();
    }

    public function amend(string $newText, string $reason, User $user): self
    {
        if ($this->status !== 'signed') {
            throw new LogicException('Only a signed note can be amended.');
        }

        $amendment = static::create([
            'encounter_id' => $this->encounter_id,
            'note_type' => $this->note_type,
            'note_text' => $newText,
            'status' => 'draft',
            'created_by' => $user->id,
            'amends_note_id' => $this->id,
            'amendment_reason' => $reason,
        ]);

        $this->forceFill(['status' => 'amended'])->save();

        return $amendment;
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function amends(): BelongsTo
    {
        return $this->belongsTo(ClinicalNote::class, 'amends_note_id');
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(ClinicalNote::class, 'amends_note_id');
    }
}
