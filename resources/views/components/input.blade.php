@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'autofocus' => false])
<div class="mb-3">
    @if(isset($label))
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <input
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        type="{{ $type ?? 'text' }}"
        value="{{ old($name, $value ?? '') }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if(isset($required) && $required) required @endif
        @if(isset($autofocus) && $autofocus) autofocus @endif
    >
    @if($errors->has($name))
        <div class="invalid-feedback">{{ $errors->first($name) }}</div>
    @endif
</div>