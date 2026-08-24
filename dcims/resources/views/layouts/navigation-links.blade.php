@php
    $user = Auth::user();
    $can = fn (string $module) => $user->canAccessModule($module);
    $masterDataActive = request()->routeIs('admin.*') && ! request()->routeIs('admin.users.*') && ! request()->routeIs('admin.roles.*');
    $prefix = $prefix ?? 'sidebar';
@endphp

<a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
    {{ __('Dashboard') }}
</a>

@if ($can('patients') || $can('appointments') || $can('queue') || $can('recalls') || $can('referrals') || $can('encounters') || $can('treatment_plans') || $can('laboratory'))
    <div class="sidebar-section">
        <div class="sidebar-section-title">{{ __('Patient Care') }}</div>
        @if ($can('patients'))
            <a class="sidebar-link {{ request()->routeIs('patients.*') ? 'active' : '' }}" href="{{ route('patients.index') }}">{{ __('Patients') }}</a>
        @endif
        @if ($can('appointments'))
            <a class="sidebar-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">{{ __('Appointments') }}</a>
        @endif
        @if ($can('queue'))
            <a class="sidebar-link {{ request()->routeIs('queue.*') ? 'active' : '' }}" href="{{ route('queue.index') }}">{{ __('Queue') }}</a>
        @endif
        @if ($can('recalls'))
            <a class="sidebar-link {{ request()->routeIs('recalls.*') ? 'active' : '' }}" href="{{ route('recalls.index') }}">{{ __('Recalls') }}</a>
        @endif
        @if ($can('referrals'))
            <a class="sidebar-link {{ request()->routeIs('referrals.*') ? 'active' : '' }}" href="{{ route('referrals.index') }}">{{ __('Referrals') }}</a>
        @endif
        @if ($can('encounters'))
            <a class="sidebar-link {{ request()->routeIs('encounters.*') ? 'active' : '' }}" href="{{ route('encounters.index') }}">{{ __('Encounters') }}</a>
        @endif
        @if ($can('treatment_plans'))
            <a class="sidebar-link {{ request()->routeIs('treatment-plans.*') ? 'active' : '' }}" href="{{ route('treatment-plans.index') }}">{{ __('Treatment Plans') }}</a>
        @endif
        @if ($can('laboratory'))
            <a class="sidebar-link {{ request()->routeIs('lab-orders.*') ? 'active' : '' }}" href="{{ route('lab-orders.index') }}">{{ __('Lab Orders') }}</a>
        @endif
    </div>
@endif

@if ($can('invoices'))
    <div class="sidebar-section">
        <div class="sidebar-section-title">{{ __('Financial') }}</div>
        <a class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">{{ __('Invoices') }}</a>
    </div>
@endif

@if ($can('inventory') || $can('purchase_orders'))
    <div class="sidebar-section">
        <div class="sidebar-section-title">{{ __('Inventory') }}</div>
        @if ($can('inventory'))
            <a class="sidebar-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">{{ __('Inventory') }}</a>
        @endif
        @if ($can('purchase_orders'))
            <a class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">{{ __('Purchase Orders') }}</a>
        @endif
    </div>
@endif

@if ($can('reports'))
    <div class="sidebar-section">
        <div class="sidebar-section-title">{{ __('Reports') }}</div>
        <a class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">{{ __('Reports') }}</a>
    </div>
@endif

@if ($can('audit_logs') || $user->is_admin || $can('master_data'))
    <div class="sidebar-section">
        <div class="sidebar-section-title">{{ __('Administration') }}</div>
        @if ($can('audit_logs'))
            <a class="sidebar-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">{{ __('Audit Log') }}</a>
        @endif
        @if ($user->is_admin)
            <a class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
            <a class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">{{ __('Roles') }}</a>
        @endif

        @if ($can('master_data'))
            <button class="sidebar-link sidebar-link-toggle {{ $masterDataActive ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $prefix }}-master-data" aria-expanded="{{ $masterDataActive ? 'true' : 'false' }}">
                {{ __('Master Data') }}
                <svg class="sidebar-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="collapse {{ $masterDataActive ? 'show' : '' }}" id="{{ $prefix }}-master-data">
                <div class="sidebar-subnav">
                    <a class="sidebar-link" href="{{ route('admin.procedure-categories.index') }}">{{ __('Procedure Categories') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.procedures.index') }}">{{ __('Procedures') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.tooth-conditions.index') }}">{{ __('Tooth Conditions') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.medications.index') }}">{{ __('Medications') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.recall-types.index') }}">{{ __('Recall Types') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.consent-types.index') }}">{{ __('Consent Types') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.providers.index') }}">{{ __('Providers') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.chairs.index') }}">{{ __('Chairs') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.appointment-types.index') }}">{{ __('Appointment Types') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.payment-methods.index') }}">{{ __('Payment Methods') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.inventory-categories.index') }}">{{ __('Inventory Categories') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.inventory-units.index') }}">{{ __('Inventory Units') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.products.index') }}">{{ __('Products') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.suppliers.index') }}">{{ __('Suppliers') }}</a>
                    <a class="sidebar-link" href="{{ route('admin.labs.index') }}">{{ __('Labs') }}</a>
                </div>
            </div>
        @endif
    </div>
@endif
