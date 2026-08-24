<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use Auditable, HasFactory;

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

    public function medicalHistory(): HasOne
    {
        return $this->hasOne(MedicalHistory::class);
    }

    public function dentalHistory(): HasOne
    {
        return $this->hasOne(DentalHistory::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(PatientCondition::class);
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(PatientAllergy::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    public function odontograms(): HasMany
    {
        return $this->hasMany(Odontogram::class);
    }

    public function perioExaminations(): HasMany
    {
        return $this->hasMany(PerioExamination::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function recalls(): HasMany
    {
        return $this->hasMany(Recall::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function labOrders(): HasMany
    {
        return $this->hasMany(LabOrder::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(EncounterDiagnosis::class);
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class);
    }

    public function procedureRecords(): HasMany
    {
        return $this->hasMany(ProcedureRecord::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
