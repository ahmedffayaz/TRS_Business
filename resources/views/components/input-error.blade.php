@props(['message'])

<span {{ $attributes->merge(['class' => 'error']) }}>{{ $message }}</span>
