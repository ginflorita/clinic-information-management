<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'preferred_name',
        'date_of_birth',
        'sex',
        'civil_status',
        'occupation',
        'email',
        'registration_date',
        'referral_source',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'registration_date' => 'date',
        'archived_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            if (empty($patient->patient_number)) {
                $patient->patient_number = static::generatePatientNumber();
            }
        });
    }

    public static function generatePatientNumber(): string
    {
        $prefix = 'PAT-'.now()->year.'-';

        $last = static::withoutGlobalScopes()
            ->where('patient_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('patient_number')
            ->value('patient_number');

        $next = $last ? ((int) substr($last, -6)) + 1 : 1;

        return $prefix.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}", ' '));
    }

    protected function age(): Attribute
    {
        return Attribute::get(fn () => $this->date_of_birth?->age);
    }

    public function archive(): void
    {
        $this->forceFill(['status' => 'archived', 'archived_at' => now()])->save();
    }

    public function restore(): void
    {
        $this->forceFill(['status' => 'active', 'archived_at' => null])->save();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(PatientAddress::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(PatientContact::class);
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(PatientRelationship::class);
    }

    public function identifiers(): HasMany
    {
        return $this->hasMany(PatientIdentifier::class);
    }
}
