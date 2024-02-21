@props(['value'])

<a {!! $attributes->merge(['class' => 'link']) !!}>
    {{ $value ?? $slot }}
</a>
