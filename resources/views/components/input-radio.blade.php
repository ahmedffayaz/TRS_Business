@props(['labelName', 'name', 'colorClass' => null, 'id', 'value' => null, 'isChecked' => false, 'wireModel'])

<div class="form-check {{ $colorClass ? $colorClass : 'form-check-inline' }}">
    <input class="form-check-input" type="radio" name="{{ $name }}" value={{ $value ? $value : '' }}
        id="{{ $id }}" wire:model="{{ $wireModel }}" {{ $isChecked ? 'checked' : '' }} />
    <label class="form-check-label" for="{{ $id }}">{{ $labelName }}</label>
</div>
