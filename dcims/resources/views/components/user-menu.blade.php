@props(['align' => 'end'])

<div {{ $attributes->merge(['class' => 'dropdown']) }}>
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ Auth::user()->name }}
    </a>
    <ul class="dropdown-menu {{ $align === 'end' ? 'dropdown-menu-end' : '' }}">
        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
            </form>
        </li>
    </ul>
</div>
