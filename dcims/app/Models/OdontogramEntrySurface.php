<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdontogramEntrySurface extends Model
{
    use HasFactory;

    public $timestamps = false;

    const SURFACES = ['M', 'D', 'O', 'I', 'B', 'L', 'P', 'F'];

    protected $fillable = [
        'odontogram_entry_id',
        'surface',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(OdontogramEntry::class, 'odontogram_entry_id');
    }
}
