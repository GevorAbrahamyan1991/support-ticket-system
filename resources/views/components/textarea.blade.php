@props(['name', 'label' => null, 'value' => null, 'rows' => 3, 'required' => false])
<div class="mb-3">
    @if(isset($label))
        <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}</label>
    @endif
    <textarea
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        rows="{{ $rows ?? 3 }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        @if(isset($required) && $required) required @endif
    >{{ old($name, $value ?? '') }}</textarea>
    @if($errors->has($name))
        <div class="invalid-feedback">{{ $errors->first($name) }}</div>
    @endif
</div>