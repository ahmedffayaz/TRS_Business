@props(['length'])
<div>
    <textarea {!! $attributes->merge(['class' => 'form-control']) !!}>{{ $value ?? $slot }}</textarea>
    @isset($length)
        <small class="textarea-counter-value float-end"><span class="char-count">0</span> / {{ $length }}</small>
    @endisset
</div>
