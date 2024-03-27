@props(['labelValue', 'id', 'statusClass' => null, 'value' => null, 'isRequired' => false])

<div class="form-check {{ $statusClass ? $statusClass : 'form-check-inline' }}">
    <input {{ $attributes->merge(['class' => 'form-check-input']) }} id="{{ $id }}" @if ($value) value="{{ $value }}" @endif />
    <x-input-label class="form-check-label {{ $isRequired ? 'required' : '' }}" for="{{ $id }}" :value="$labelValue" />
</div>
