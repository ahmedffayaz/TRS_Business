@props(['value', 'id', 'statusClass' => null])
<div>
    <div class="form-check {{ $statusClass ? $statusClass : 'form-check-inline' }}">
        <input {{ $attributes->merge(['class' => 'form-check-input']) }} id="{{ $id }}" />
        <x-input-label class="form-check-label" for="{{ $id }}" :value="$value" />
    </div>
    <small class="error-message"></small>
</div>
