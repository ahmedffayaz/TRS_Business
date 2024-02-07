<div>
    <select {{ $attributes->merge(['class' => 'form-select']) }}>
        {{ $slot }}
    </select>
    <small class="error-message"></small>
</div>
