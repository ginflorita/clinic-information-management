<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecallType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'default_interval_months',
        'is_active',
    ];

    protected $casts = [
        'default_interval_months' => 'integer',
        'is_active' => 'boolean',
    ];

    public function recalls(): HasMany
    {
        return $this->hasMany(Recall::class);
    }
}
