@assets
    <style>
        html .content {
            margin-left: 0px;
        }

        .header-navbar.floating-nav {
            right: auto;
            margin-left: -27px;
        }

        .max-width-signature {
            max-width: 400px;
        }
    </style>
@endassets
<div>
    <div class="card">
        <div class="card-body py-1 my-25">
            <h1 class="card-title">{{ $termsConditions->title }}</h1>
            <div class="row mb-2">
                <div class="col-md-12 col-sm-6 ">
                    {{$termsConditions->description}}
                </div>
            </div>
            <form wire:submit.prevent="acceptTerms('{{ $termsConditions?->id }}')">
                <div class="row mb-2">
                    <div class="col-md-6 col-lg-3 col-xl-3 mt-3">
                        <x-input-label for="user-signature" value="Upload Signature" />
                        <x-input type="file" :class="$errors->has('form.signatue_file') ? 'error' : ''"
                            id="user-signature" value="{{auth()->user()->name}}" wire:model="form.signature_file" />
                        @error('form.signature_file')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                @if ($form->signature_file)
                <div class="row mb-2">
                    <img src="{{ $form?->signature_file?->temporaryUrl() }}" id="favicon-img"
                        class="max-width-signature" alt="Business favicon">
                </div>
                @endif
                <div class="row mb-2">
                    <div class="col-md-12 col-sm-6 ">
                        <div class="demo-inline-spacing">
                            <div class="custom-control custom-checkbox">
                                <x-input-checkbox type="checkbox" id="is_accept" name="is_accept" wire:model="form.is_accept"
                                    :class="$errors->has('form.is_accept') ? 'error' : ''"
                                    :value="__('Accept Terms & Conditions')" />
                                @error('form.is_accept')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-6 text-start">
                        <button class="btn btn-primary me-1 waves-effect waves-float waves-light" tabindex="4"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>{{  _('Accept')}}</span>
                            <span wire:loading>
                                <i class="fa fa-spinner fa-spin " wire:ignore></i> {{ __('Loading...') }}
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
