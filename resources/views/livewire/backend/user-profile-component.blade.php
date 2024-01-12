<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Profile Details</h4>
                </div>
                <div class="card-body  py-2 my-25">
                    @livewire('partials.file-upload-component')
                    <form class="form form-horizontal" wire:submit.prevent="submit">
                        @csrf
                        <div class="row mt-2 pt-50">
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountFirstName">First Name</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" wire:model="first_name" placeholder="John" >
                                @error('first_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountLastName">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" wire:model="last_name" placeholder="Doe">
                                @error('last_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountEmail">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="Email">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountPhoneNumber">Phone Number</label>
                                <input type="text" class="form-control account-number-mask @error('phone') is-invalid @enderror" wire:model="phone" placeholder="Phone Number">
                                @error('phone')
                                    <small class="text-danger">{{ $phone }}</small>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountPhoneNumber">Alternative Phone Number</label>
                                <input type="text" class="form-control account-number-mask @error('alternative_number') is-invalid @enderror" wire:model="alternative_number"  placeholder="Alternative Phone Number">
                                @error('alternative_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountAddress">Address</label>
                                <textarea row="3" type="text" class="form-control @error('address') is-invalid @enderror" wire:model="address" placeholder="Your Address"></textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-primary me-1 waves-effect waves-float waves-light" tabindex="4" wire:loading.attr="disabled">
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
