@csrf

<div class="mb-3">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $user->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-1" />
</div>

<div class="mb-3">
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" name="email" type="email" :value="old('email', $user->email ?? '')" required autocomplete="username" />
    <x-input-error :messages="$errors->get('email')" class="mt-1" />
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <x-input-label for="password" :value="isset($user) ? 'New Password' : 'Password'" />
        <x-text-input id="password" name="password" type="password" autocomplete="new-password" :required="! isset($user)" />
        @if (isset($user))
            <small class="text-secondary d-block mt-1">Leave blank to keep the current password.</small>
        @endif
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>
    <div class="col-md-6 mb-3">
        <x-input-label for="password_confirmation" value="Confirm Password" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" :required="! isset($user)" />
    </div>
</div>

<div class="mb-3 form-check">
    <input id="is_admin" type="checkbox" name="is_admin" value="1" class="form-check-input" @checked(old('is_admin', $user->is_admin ?? false))>
    <label class="form-check-label" for="is_admin">Administrator (can manage users)</label>
</div>

@if (isset($user))
    <div class="mb-3 form-check">
        <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $user->is_active))>
        <label class="form-check-label" for="is_active">Active (can log in)</label>
    </div>
@endif

<div class="d-flex gap-2">
    <x-primary-button>Save</x-primary-button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
