<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tooth extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'notation_system',
        'tooth_code',
        'tooth_name',
        'dentition_type',
        'arch',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function odontogramEntries(): HasMany
    {
        return $this->hasMany(OdontogramEntry::class);
    }
}
