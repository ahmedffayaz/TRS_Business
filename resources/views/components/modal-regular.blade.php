@props(['wireIgnoreSelf', 'class'])
<div {{ $wireIgnoreSelf }} class="modal fade text-start" id="defaultSize" tabindex="-1" aria-labelledby="myModalLabel18" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $class ?? '' }}">
        {{ $slot }}
    </div>
</div>
