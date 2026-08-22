<nav class="navbar navbar-expand-sm navbar-light bg-white border-bottom">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-inline-flex" href="{{ route('dashboard') }}">
            <span class="bg-dark rounded d-inline-flex align-items-center px-2 py-1">
                <img src="{{ asset('images/ginflorita-logo.png') }}" alt="DCIMS" style="height: 1.75rem; width: auto;">
            </span>
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
                <li class="nav-item">
                    <x-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')">
                        {{ __('Inventory') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">
                        {{ __('Purchase Orders') }}
                    </x-nav-link>
                </li>
                <li class="nav-item">
                    <x-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                        {{ __('Audit Log') }}
                    </x-nav-link>
                </li>
                @if (Auth::user()->is_admin)
                    <li class="nav-item">
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Users') }}
                        </x-nav-link>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Master Data') }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.procedure-categories.index') }}">{{ __('Procedure Categories') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.procedures.index') }}">{{ __('Procedures') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.tooth-conditions.index') }}">{{ __('Tooth Conditions') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.medications.index') }}">{{ __('Medications') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.providers.index') }}">{{ __('Providers') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.chairs.index') }}">{{ __('Chairs') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.appointment-types.index') }}">{{ __('Appointment Types') }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('admin.payment-methods.index') }}">{{ __('Payment Methods') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.inventory-categories.index') }}">{{ __('Inventory Categories') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.inventory-units.index') }}">{{ __('Inventory Units') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.suppliers.index') }}">{{ __('Suppliers') }}</a></li>
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
