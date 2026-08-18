<nav class="navbar navbar-expand-sm navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo style="height: 2rem; width: auto;" class="text-secondary" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                        {{ __('Patients') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
                        {{ __('Appointments') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('queue.index')" :active="request()->routeIs('queue.*')">
                        {{ __('Queue') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('encounters.index')" :active="request()->routeIs('encounters.*')">
                        {{ __('Encounters') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('treatment-plans.index')" :active="request()->routeIs('treatment-plans.*')">
                        {{ __('Treatment Plans') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                        {{ __('Invoices') }}
                    </x-nav-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Master Data') }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.procedure-categories.index') }}">{{ __('Procedure Categories') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.procedures.index') }}">{{ __('Procedures') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.tooth-conditions.index') }}">{{ __('Tooth Conditions') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.providers.index') }}">{{ __('Providers') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.chairs.index') }}">{{ __('Chairs') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.appointment-types.index') }}">{{ __('Appointment Types') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.payment-methods.index') }}">{{ __('Payment Methods') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.inventory-categories.index') }}">{{ __('Inventory Categories') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.inventory-units.index') }}">{{ __('Inventory Units') }}</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
