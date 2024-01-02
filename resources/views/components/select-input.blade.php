<div>
    <select {{ $attributes->merge(['class' => 'form-select']) }}>
        {{ $slot }}
    </select>
</div>
