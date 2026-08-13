<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Procedure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'procedure_category_id',
        'code',
        'name',
        'description',
        'default_fee',
        'default_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'default_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProcedureCategory::class, 'procedure_category_id');
    }
}
