@props([
    'name',
    'show' => false,
])

<div class="modal fade @if($show) show @endif" id="{{ $name }}" tabindex="-1" aria-hidden="{{ $show ? 'false' : 'true' }}" @if($show) style="display: block;" @endif>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
@if($show)
    <div class="modal-backdrop fade show"></div>
@endif
