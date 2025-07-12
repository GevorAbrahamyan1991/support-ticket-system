@props(['type' => 'button', 'class' => 'btn-primary', 'icon' => null, 'id' => null, 'href' => null])
@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn ' . $class, 'id' => $id]) }}>
    @if($icon)
        <i class="bi bi-{{ $icon }}"></i>
    @endif
    {{ $slot }}
</a>
@else
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn ' . $class, 'id' => $id]) }}>
    @if($icon)
        <i class="bi bi-{{ $icon }}"></i>
    @endif
    {{ $slot }}
    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="{{ $id ? $id . '-spinner' : 'button-spinner' }}"></span>
</button>
@endif