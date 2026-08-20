<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DCIMS') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/vendor.css', 'resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-4">
            <div class="mb-4">
                <a href="/" class="d-inline-flex">
                    <span class="bg-dark rounded d-inline-flex align-items-center px-3 py-2">
                        <img src="{{ asset('images/ginflorita-logo.png') }}" alt="DCIMS" style="height: 2.25rem; width: auto;">
                    </span>
                </a>
            </div>

            <div class="w-100 px-4 py-4 bg-white shadow-sm rounded" style="max-width: 28rem;">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
