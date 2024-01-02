@props(['wireIgnoreSelf'])
<div {{ $wireIgnoreSelf }} class="modal modal-slide-in fade" id="modal-offcanvas" data-bs-backdrop="false" data-bs-scroll="false">
    <div class="modal-dialog">
        {{ $slot }}
    </div>
</div>
