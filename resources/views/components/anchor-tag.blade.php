@props(['value'])

<a {!! $attributes->merge(['class' => 'link']) !!}>
    {{ $value ?? $slug }}
</a>
