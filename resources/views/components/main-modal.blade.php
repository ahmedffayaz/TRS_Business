<!-- resources/views/modal.blade.php -->

@props([
    'wireIgnoreSelf',
    'closeModal' => null,
    'modalSize' => 'modal-lg',
    'modalTitle' => null,
    'buttonLabel' => null, // Default button label
    'buttonType' => 'submit', // Default button type
    'buttonAttributes' => [], // Any additional button attributes
    'formSubmit' => null,
    'multipart' => null,
    'buttonStatus' => null,
])

<div {{ $wireIgnoreSelf }} class="modal fade" id="main-modal" tabindex="-1" data-bs-backdrop="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $modalSize }}">
        <div class="modal-content">
            <div class="modal-header @if (empty($modalTitle)) bg-white @endif">
                @if (!empty($modalTitle))
                    <h4 class="modal-title" id="myModalLabel1">{{ $modalTitle }}</h4>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="{{ $closeModal ? $closeModal : 'closeMainModal' }}"></button>
            </div>

            <form wire:submit.prevent="{{ $formSubmit }}" enctype="{{ $multipart }}">
                <div class="modal-body px-sm-2 pb-2">

                    {{ $slot }}
                </div>
                @if ($buttonStatus == true)
                    <div class="modal-footer">
                        <x-button class="btn btn-primary waves-effect waves-float waves-light" type="{{ $buttonType }}" {{ $attributes->merge($buttonAttributes) }}>
                            <span wire:loading.remove>{{ $buttonLabel }}</span>
                            <span wire:loading>
                                <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                            </span>
                        </x-button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
