<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerioSiteMeasurement extends Model
{
    use HasFactory;

    public $timestamps = false;

    const SITES = ['mesial', 'mid', 'distal'];

    protected $fillable = [
        'perio_tooth_record_id',
        'site',
        'probing_depth',
        'gingival_recession',
        'clinical_attachment_level',
        'gingival_margin',
        'bleeding_on_probing',
        'plaque_present',
    ];

    protected $casts = [
        'probing_depth' => 'decimal:1',
        'gingival_recession' => 'decimal:1',
        'clinical_attachment_level' => 'decimal:1',
        'gingival_margin' => 'decimal:1',
        'bleeding_on_probing' => 'boolean',
        'plaque_present' => 'boolean',
    ];

    public function toothRecord(): BelongsTo
    {
        return $this->belongsTo(PerioToothRecord::class, 'perio_tooth_record_id');
    }
}
