<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'condition_name',
        'status',
        'diagnosed_date',
        'notes',
    ];

    protected $casts = [
        'diagnosed_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
