@props(['name', 'label' => null, 'required' => false, 'id' => null])
<div class="mb-3">
    @if(isset($label))
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <select
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'form-select' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if(isset($required) && $required) required @endif
    >
        {{ $slot }}
    </select>
    @if($errors->has($name))
        <div class="invalid-feedback">{{ $errors->first($name) }}</div>
    @endif
</div>