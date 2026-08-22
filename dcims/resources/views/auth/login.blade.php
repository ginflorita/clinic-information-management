<x-auth-split-layout>
    <h2 class="h4 fw-semibold mb-1">Welcome back</h2>
    <p class="text-secondary small mb-4">Sign in to your DCIMS account to continue.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
        </div>

        <div class="d-flex align-items-center justify-content-end">
            @if (Route::has('password.request'))
                <a class="text-decoration-underline small text-secondary me-3" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <p class="text-secondary small text-center mt-4 mb-0">
        {{ __('To request access to the demo system, please contact the developer at') }}
        <a href="mailto:ask.ginflorita@gmail.com" class="text-decoration-underline">ask.ginflorita@gmail.com</a>.
    </p>
</x-auth-split-layout>
