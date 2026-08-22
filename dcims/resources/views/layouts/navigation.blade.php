{{-- Desktop sidebar --}}
<aside class="app-sidebar d-none d-lg-flex flex-column">
    <a class="sidebar-brand" href="{{ route('dashboard') }}">
        <span class="bg-dark rounded d-inline-flex align-items-center px-2 py-1">
            <img src="{{ asset('images/ginflorita-logo.png') }}" alt="DCIMS" style="height: 1.75rem; width: auto;">
        </span>
    </a>

    <nav class="sidebar-nav flex-grow-1">
        @include('layouts.navigation-links', ['prefix' => 'desktop'])
    </nav>

    <div class="sidebar-user dropdown">
        <a class="sidebar-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ Auth::user()->name }}
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                </form>
            </li>
        </ul>
    </div>
</aside>

{{-- Mobile top bar --}}
<nav class="navbar navbar-light bg-white border-bottom d-lg-none">
    <div class="container-fluid px-4">
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Toggle navigation">
            <span style="display: block; width: 1.25rem; height: 2px; background: currentColor; margin: 4px 0;"></span>
            <span style="display: block; width: 1.25rem; height: 2px; background: currentColor; margin: 4px 0;"></span>
            <span style="display: block; width: 1.25rem; height: 2px; background: currentColor; margin: 4px 0;"></span>
        </button>

        <a class="navbar-brand d-inline-flex mx-auto" href="{{ route('dashboard') }}">
            <span class="bg-dark rounded d-inline-flex align-items-center px-2 py-1">
                <img src="{{ asset('images/ginflorita-logo.png') }}" alt="DCIMS" style="height: 1.5rem; width: auto;">
            </span>
        </a>

        <div class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Mobile off-canvas sidebar --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <span class="bg-dark rounded d-inline-flex align-items-center px-2 py-1">
            <img src="{{ asset('images/ginflorita-logo.png') }}" alt="DCIMS" style="height: 1.75rem; width: auto;">
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body sidebar-nav">
        @include('layouts.navigation-links', ['prefix' => 'mobile'])
    </div>
</div>
