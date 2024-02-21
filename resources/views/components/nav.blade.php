@props(['value'])
<li {!! $attributes->merge(['class' => '']) !!}>
    {{ $value ?? $slot }}
</li>
