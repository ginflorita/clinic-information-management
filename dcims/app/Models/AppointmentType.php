<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'default_duration_minutes', 'color', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
