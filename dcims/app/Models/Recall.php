<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class Recall extends Model
{
    use Auditable, HasFactory;

    const STATUSES = ['pending', 'completed', 'cancelled'];

    protected $fillable = [
        'patient_id',
        'recall_type_id',
        'due_date',
        'completed_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_date' => 'date',
    ];

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    public function complete(): void
    {
        if ($this->status !== 'pending') {
            throw new LogicException("Cannot complete a {$this->status} recall.");
        }

        $this->update(['status' => 'completed', 'completed_date' => now()->toDateString()]);
    }

    public function cancel(): void
    {
        if ($this->status !== 'pending') {
            throw new LogicException("Cannot cancel a {$this->status} recall.");
        }

        $this->update(['status' => 'cancelled']);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function recallType(): BelongsTo
    {
        return $this->belongsTo(RecallType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
