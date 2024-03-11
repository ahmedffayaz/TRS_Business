@props(['value', 'id'])
<div>
    <input {{ $attributes->merge(['class' => 'custom-control-input']) }} id="{{ $id }}" />
    <x-input-label class="custom-control-label" for="{{ $id }}" :value="$value" />
    <small class="error-message"></small>
</div>
