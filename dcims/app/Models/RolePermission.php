<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use Auditable, HasFactory;

    protected $fillable = [
        'role_id',
        'module',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
