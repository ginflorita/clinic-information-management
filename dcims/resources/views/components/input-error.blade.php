@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-danger small mb-0 ps-0']) }} style="list-style: none;">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
