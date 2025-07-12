<div class="alert alert-{{ $type ?? 'info' }} {{ $dismissible ?? false ? 'alert-dismissible fade show' : '' }}" role="alert">
    {{ $slot }}
    @if($dismissible ?? false)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>