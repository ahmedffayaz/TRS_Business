<div>
    @section('breadcrumbs', Breadcrumbs::render('user_profile'))
    <section id="basic-horizontal-layouts">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Profile Details</h4>
                    </div>
                    <div class="card-body  py-2 my-25">
                        <form class="form form-horizontal" wire:submit.prevent="update">
                            @csrf
                            <div class="d-flex">
                                <a href="#" class="me-25">
                                    <img src="{{ $form->avatar
                                        ? $form?->avatar?->temporaryUrl()
                                        : (isset($profileLogo) && $profileLogo
                                            ? asset('storage/profile/' . $profileLogo)
                                            : asset($defaultImage)) }}"
                                        id="account-upload-img" class="uploadedAvatar rounded me-50"
                                        alt="profile image" height="100" width="100">
                                </a>
                                <div class="d-flex align-items-end mt-75 ms-1">
                                    <div>
                                        <label for="account-upload"
                                            class="btn btn-sm btn-primary mb-75 me-75 waves-effect waves-float waves-light">Upload</label>
                                        <input type="file" wire:model="form.avatar" id="account-upload" hidden="" accept="image/*">
                                        <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2 pt-50">
                                <div class="col-12 col-sm-6 mb-1">
                                    <x-input-label for="accountFirstName" class="required" value="First Name" />
                                    <x-input type="text" :class="$errors->has('form.first_name') ? 'is-invalid' : ''" wire:model="form.first_name"
                                        placeholder="John" />
                                    @error('form.first_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 col-sm-6 mb-1">
                                    <x-input-label for="accountLastName" class="required" value="Last Name" />
                                    <x-input type="text" :class="$errors->has('form.last_name') ? 'is-invalid' : ''" wire:model="form.last_name"
                                        placeholder="Doe" />
                                    @error('form.last_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 col-sm-6 mb-1">
                                    <x-input-label for="accountEmail" class="required" value="Email" />
                                    <x-input type="email" :class="$errors->has('form.email') ? 'is-invalid' : ''" wire:model="form.email"
                                        placeholder="Email" />
                                    @error('form.email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 col-sm-6 mb-1">
                                    <x-input-label for="accountPhoneNumber" class="required" value="Phone Number" />
                                    <x-input type="text" :class="$errors->has('form.phone')
                                        ? 'account-number-mask is-invalid'
                                        : 'account-number-mask'" wire:model="form.phone"
                                        placeholder="Phone Number" />
                                    @error('form.phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 col-sm-6 mb-1">
                                    <x-input-label for="accountPhoneNumber" value="Alternative Phone Number" />
                                    <x-input type="text" :class="$errors->has('form.alternative_number')
                                        ? 'account-number-mask is-invalid'
                                        : 'account-number-mask'" wire:model="form.alternative_number"
                                        placeholder="Alternative Phone Number" />
                                    @error('form.alternative_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 col-sm-6 mb-1">
                                    <x-input-label for="accountAddress" class="required" value="Address" />
                                    <x-textarea :class="$errors->has('form.address')
                                        ? 'is-invalid char-textarea'
                                        : 'char-textarea'" data-length="200" length="200" rows="3"
                                        wire:model="form.address" placeholder="Your Address"></x-textarea>
                                    @error('form.address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-primary me-1 waves-effect waves-float waves-light"
                                        tabindex="4" wire:loading.attr="disabled">
                                        <span wire:loading.remove>{{ __('Save changes') }}</span>
                                        <span wire:loading>
                                            <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
