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
    <body>
        <div class="min-vh-100 d-flex flex-column flex-lg-row">
            <div class="auth-brand-panel d-flex flex-column justify-content-between text-white p-4 p-lg-5">
                <a href="/" class="d-inline-flex align-self-start">
                    <span class="bg-white bg-opacity-10 rounded d-inline-flex align-items-center px-3 py-2">
                        <img src="{{ asset('images/ginflorita-logo.png') }}" alt="DCIMS" style="height: 2rem; width: auto;">
                    </span>
                </a>

                <div class="py-4 py-lg-0">
                    <div class="auth-brand-icon mb-4 d-none d-lg-flex">
                        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2.5c-2.2 0-3.2 1.15-4.6 1.15-1.35 0-2.3-.9-3.35.15-1.05 1.05-1.15 3-.7 4.95.45 1.95 1.45 3.55 1.8 5.6.3 1.7.6 4.2 2.1 4.2 1.45 0 1.45-2.65 1.8-4.25.3-1.25.65-2.25 1.55-2.25s1.25 1 1.55 2.25c.35 1.6.35 4.25 1.8 4.25 1.5 0 1.8-2.5 2.1-4.2.35-2.05 1.35-3.65 1.8-5.6.45-1.95.35-3.9-.7-4.95-1.05-1.05-2-.15-3.35-.15-1.4 0-2.4-1.15-4.6-1.15z" stroke="white" stroke-width="1.3" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <h1 class="fw-semibold mb-2" style="font-size: 1.85rem; line-height: 1.25;">Dental Clinic Information Management System</h1>
                    <p class="opacity-75 mb-4" style="max-width: 26rem;">One place for patient records, dental charting, treatment plans, and billing — built for the day-to-day of running a dental clinic.</p>

                    <ul class="list-unstyled d-none d-lg-block mb-0">
                        <li class="d-flex align-items-center mb-3">
                            <span class="auth-feature-icon me-3">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="white" stroke-width="1.4"/></svg>
                            </span>
                            <span class="opacity-90 small">Patient records &amp; dental/periodontal charting</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <span class="auth-feature-icon me-3">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="white" stroke-width="1.4"/></svg>
                            </span>
                            <span class="opacity-90 small">Appointments, queueing &amp; treatment planning</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <span class="auth-feature-icon me-3">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="white" stroke-width="1.4"/></svg>
                            </span>
                            <span class="opacity-90 small">Billing, inventory &amp; a full audit trail</span>
                        </li>
                    </ul>
                </div>

                <p class="small opacity-50 mb-0">&copy; {{ date('Y') }} DCIMS</p>
            </div>

            <div class="flex-fill d-flex align-items-center justify-content-center p-4 py-5 bg-white">
                <div class="w-100" style="max-width: 24rem;">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
