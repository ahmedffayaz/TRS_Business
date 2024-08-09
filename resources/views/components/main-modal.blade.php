@props(['wireIgnoreSelf', 'closeModal' => null, 'modalSize' => 'modal-lg', 'modalTitle' => null])
<div {{ $wireIgnoreSelf }} class="modal  fade" id="main-modal" tabindex="-1" data-bs-backdrop="false"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{$modalSize}}">
        <div class="modal-content">
            <div class="modal-header @if(!empty($modalTitle)) @else bg-white @endif">
                @if (!empty($modalTitle))
                    <h4 class="modal-title" id="myModalLabel1">{{ $modalTitle }}</h4>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    wire:click="{{ $closeModal ? $closeModal : 'closeMainModal' }}"></button>
            </div>
            <div class="modal-body px-sm-2 pb-2">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
