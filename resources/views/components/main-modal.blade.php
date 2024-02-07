@props(['wireIgnoreSelf'])
<div {{ $wireIgnoreSelf }} class="modal  fade" id="main-modal" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg ">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeMainModal"></button>
            </div>
            <div class="modal-body px-sm-5 pb-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
