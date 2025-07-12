@props(['type' => 'secondary', 'icon' => null, 'class' => null])
@php
    $color = $type ?? 'secondary';
    $colorClass = 'bg-' . str_replace(['bg-', ' '], ['', ' '], $color);
    if(strpos($color, 'warning') !== false && strpos($color, 'text-dark') !== false) {
        $colorClass = 'bg-warning text-dark';
    }
@endphp
<span class="badge {{ $colorClass }} {{ $class ?? '' }}">
    @if($icon)<i class="bi bi-{{ $icon }}"></i> @endif
    {{ $slot }}
</span>