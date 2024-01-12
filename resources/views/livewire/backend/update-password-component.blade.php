<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Update Password</h4>
                </div>
                <div class="card-body  py-2 my-25">
                    <form class="form form-horizontal" wire:submit.prevent="submit">
                        @csrf
                        <div class="row">
                            <div class="col-6 mb-1">
                                <label class="col-form-label" for="old_password">Old Password</label>
                                <input type="text" id="old_password" class="form-control @error('old_password') is-invalid @enderror" wire:model="old_password"
                                    placeholder="Old Password">
                                @error('old_password')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-6 mb-1">
                                <label class="col-form-label" for="email-id">New Password</label>
                                <input type="text" id="password" class="form-control @error('password') is-invalid @enderror" wire:model="password"
                                    placeholder="New Password">
                                @error('password')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-6 mb-1">
                                <label class="col-form-label" for="password_confirmation">Confirm Password</label>
                                <input type="text" class="form-control @error('password_confirmation') is-invalid @enderror" wire:model="password_confirmation"
                                    placeholder="Confirmed Password">
                                @error('password_confirmation')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary me-1 waves-effect waves-float waves-light">Update Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
