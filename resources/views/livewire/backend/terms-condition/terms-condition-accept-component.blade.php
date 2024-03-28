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

        /* Hide the textarea */
        textarea[name="description"] {
            display: none;
        }

        @media screen and (max-width: 539px) {
            .card {
                width: 100%;
            }
        }

        @media screen and (min-width: 540px) {
            .card {
                width: 600px;
            }
        }
    </style>
@endassets
<div>
    <div class="d-flex justify-content-center">
        <div class="card">
            <div class="card-body py-1 my-25">
                <h1 class="card-title text-center">{{ $termsConditions?->title }} - v{{ $termsConditions?->version }} - {{ $termsConditions?->business?->name }}</h1>
                <div class="row mb-2">
                    <div class="col-md-12 col-sm-6 ">
                        <x-textarea name="description" id="count_text" rows="4">{{ $termsConditions?->description }}</x-textarea>
                        <span wire:ignore.>
                            <div class="form-group markup-preview"></div>
                        </span>
                    </div>
                </div>
                <form wire:submit.prevent="acceptTerms('{{ $termsConditions?->id }}')">
                    <div class="row mb-2 mt-3">
                        <div class="col-md-12">
                            <x-input-label for="signature-type" class="required me-4" value="Upload Signature" />
                            <div class="btn-group">
                                <button class="btn btn-gradient-primary dropdown-toggle" type="button" id="dropdownMenuButton101" data-bs-toggle="dropdown" aria-expanded="false">
                                    Upload
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton101">
                                    <a class="dropdown-item" href="#" id="upload-sign">Upload</a>
                                    <a class="dropdown-item" href="#" id="draw-sign">Draw</a>
                                </div>
                            </div>
                            <x-input accept="image/*" type="file" :class="$errors->has('form.signatue_file') ? 'error d-none' : 'd-none'"
                                id="digital-signature-input" value="{{auth()->user()->name}}" wire:model="form.signature_file" />
                            <x-input type="hidden" name="digital_signature_pad" wire:model="form.digital_signature_pad" />
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
                    <span wire:ignore.>
                    <div class="row mb-2">
                        <img src="" class="max-width-signature user-sign" />
                    </div>
                    </span>
                    <div class="row mb-2">
                        <div class="col-md-12 col-sm-6 ">
                            <x-input-checkbox type="checkbox" id="is_accept" name="is_accept"
                                    wire:model="form.is_accept" statusClass="form-check-success"
                                :class="$errors->has('form.is_accept') ? 'error' : ''"
                                :labelValue="__('Accept Terms & Conditions')" isRequired=true />
                            @error('form.is_accept')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-6 text-start">
                            <button class="btn btn-primary me-1 waves-effect waves-float waves-light" tabindex="4"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>{{  _('Accept')}}</span>
                                <x-button-loader />
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-main-modal wireIgnoreSelf="wire:ignore.self" modalSize="modal-sm">
        <h4 class="modal-title" id="myModalLabel">Draw Signature</h4>
        <form id="signature-image-form">
            <div class="row mb-2">
                <div class="col-md-12 border border-2 p-1">
                    <canvas id="sign-canvas" width="280" height="100" onClick="$('#sign-submit-btn').attr('disabled', false);">
                        This Browser doesn't support canvas
                    </canvas>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-outline-primary me-1 waves-effect waves-float waves-light px-4" id="sign-clear-btn"
                        onClick="$('#sig-submit-btn').attr('disabled', true);">Clear</button>
                    <button class="btn btn-primary waves-effect waves-float waves-light float-end px-4" id="sign-submit-btn"
                        onClick="$('#sig-submit-btn').attr('disabled', true);">Draw</button>
                </div>
            </div>
        </form>
    </x-main-modal>
</div>

@script
    <script type="module">
        $(document).ready(function () {
            initializeMTE();

            // Initialize markup text editor
            function initializeMTE()
            {
                var $textareaName = "description";
                Livewire.dispatch('initialize-markup-editor', {'textarea': $textareaName, 'heading' : false});

                let $description = $('textarea[name="description"]').val();
                Livewire.dispatch('markup-editor-change', {'textarea' : $textareaName, 'value' : $description});
            }

            // Upload image
            $(document).on('click', '#upload-sign', function(e) {
                $('#digital-signature-input').click();
            });

            // Draw signature
            $('#draw-sign').on('click', function (event) {
                event.preventDefault();
                Livewire.dispatch('open-main-modal');
                let imageStoragePath = "{{ asset('storage/') }}";
                Livewire.dispatch('digital-signature-pad', {
                    'canvas_id' : 'sign-canvas',
                    'ctx_strokes_style' : '#222222',
                    'ctx_line_width' : 4,
                    'clear_button_id' : 'sign-clear-btn',
                    'sign_submit_button_id' : 'sign-submit-btn',
                    'form_id' : 'signature-image-form',
                    'digital_signature_hidden_input_name' : 'digital_signature_pad',
                    'url' : "{{ route('dashboard.upload-digital-image') }}",
                    'storage_path' : imageStoragePath
                });
            });
        })
    </script>
@endscript
