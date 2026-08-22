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
        <div class="d-flex align-items-stretch" style="min-height: 100vh;">
            @include('layouts.navigation')

            <div class="flex-grow-1 d-flex flex-column min-vw-0">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white border-bottom py-3">
                        <div class="container-fluid px-4 d-flex justify-content-between align-items-center gap-3">
                            <div class="min-w-0">
                                {{ $header }}
                            </div>
                            <x-user-menu class="d-none d-lg-block flex-shrink-0" />
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-grow-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
