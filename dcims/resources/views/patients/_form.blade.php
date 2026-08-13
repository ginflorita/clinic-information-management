@csrf

<div class="row">
    <div class="col-md-4 mb-3">
        <x-input-label for="first_name" value="First Name" />
        <x-text-input id="first_name" name="first_name" type="text" :value="old('first_name', $patient->first_name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="middle_name" value="Middle Name" />
        <x-text-input id="middle_name" name="middle_name" type="text" :value="old('middle_name', $patient->middle_name ?? '')" />
        <x-input-error :messages="$errors->get('middle_name')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="last_name" value="Last Name" />
        <x-text-input id="last_name" name="last_name" type="text" :value="old('last_name', $patient->last_name ?? '')" required />
        <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <x-input-label for="suffix" value="Suffix" />
        <x-text-input id="suffix" name="suffix" type="text" :value="old('suffix', $patient->suffix ?? '')" />
        <x-input-error :messages="$errors->get('suffix')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="preferred_name" value="Preferred Name" />
        <x-text-input id="preferred_name" name="preferred_name" type="text" :value="old('preferred_name', $patient->preferred_name ?? '')" />
        <x-input-error :messages="$errors->get('preferred_name')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="date_of_birth" value="Date of Birth" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" :value="old('date_of_birth', optional($patient->date_of_birth ?? null)->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1" />
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <x-input-label for="sex" value="Sex" />
        <select id="sex" name="sex" class="form-select" required>
            <option value=""></option>
            <option value="male" @selected(old('sex', $patient->sex ?? '') === 'male')>Male</option>
            <option value="female" @selected(old('sex', $patient->sex ?? '') === 'female')>Female</option>
        </select>
        <x-input-error :messages="$errors->get('sex')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="civil_status" value="Civil Status" />
        <select id="civil_status" name="civil_status" class="form-select">
            <option value=""></option>
            @foreach (['single', 'married', 'widowed', 'separated'] as $status)
                <option value="{{ $status }}" @selected(old('civil_status', $patient->civil_status ?? '') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('civil_status')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="occupation" value="Occupation" />
        <x-text-input id="occupation" name="occupation" type="text" :value="old('occupation', $patient->occupation ?? '')" />
        <x-input-error :messages="$errors->get('occupation')" class="mt-1" />
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" :value="old('email', $patient->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="registration_date" value="Registration Date" />
        <x-text-input id="registration_date" name="registration_date" type="date" :value="old('registration_date', optional($patient->registration_date ?? now())->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('registration_date')" class="mt-1" />
    </div>
    <div class="col-md-4 mb-3">
        <x-input-label for="referral_source" value="Referral Source" />
        <x-text-input id="referral_source" name="referral_source" type="text" :value="old('referral_source', $patient->referral_source ?? '')" />
        <x-input-error :messages="$errors->get('referral_source')" class="mt-1" />
    </div>
</div>

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ isset($patient) ? route('patients.show', $patient) : route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
