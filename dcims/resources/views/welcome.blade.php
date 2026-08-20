<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'DCIMS') }}</title>

        @vite(['resources/css/vendor.css', 'resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center text-center px-3">
            <h1 class="fw-semibold mb-3">{{ config('app.name', 'DCIMS') }}</h1>
            <p class="text-secondary mb-4">Dental Clinic Information Management System</p>

            <div class="d-flex gap-2">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </body>
</html>
