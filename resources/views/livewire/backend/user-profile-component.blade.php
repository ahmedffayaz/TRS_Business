<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Profile Details</h4>
                </div>
                <div class="card-body  py-2 my-25">
                    <form class="form form-horizontal" wire:submit.prevent="submit">
                        @csrf
                        <div class="d-flex">
                            <a href="#" class="me-25">
                                <img src="{{ asset('assets/images/avatar.png') }}" id="account-upload-img" class="uploadedAvatar rounded me-50" alt="profile image"
                                    height="100" width="100">
                            </a>
                            <!-- upload and reset button -->
                            <div class="d-flex align-items-end mt-75 ms-1">
                                <div>
                                    <label for="account-upload" class="btn btn-sm btn-primary mb-75 me-75 waves-effect waves-float waves-light">Upload</label>
                                    <input type="file" id="account-upload" hidden="" accept="image/*">
                                    <button type="button" id="account-reset" class="btn btn-sm btn-outline-secondary mb-75 waves-effect">Reset</button>
                                    <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
                                </div>
                            </div>
                            <!--/ upload and reset button -->
                        </div>
                        <div class="row mt-2 pt-50">
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountFirstName">First Name</label>
                                <input type="text" class="form-control" placeholder="John">
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountLastName">Last Name</label>
                                <input type="text" class="form-control" placeholder="Doe">
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountEmail">Email</label>
                                <input type="email" class="form-control" placeholder="Email">
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountPhoneNumber">Phone Number</label>
                                <input type="text" class="form-control account-number-mask" placeholder="Phone Number">
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountPhoneNumber">Alternative Phone Number</label>
                                <input type="text" class="form-control account-number-mask" placeholder="Alternative Phone Number">
                            </div>
                            <div class="col-12 col-sm-6 mb-1">
                                <label class="form-label" for="accountAddress">Address</label>
                                <textarea row="3" type="text" class="form-control" placeholder="Your Address"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mt-1 me-1 waves-effect waves-float waves-light">Save changes</button>
                                <button type="reset" class="btn btn-outline-secondary mt-1 waves-effect">Discard</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
