<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Setting Details</h4>
                </div>
                <div class="card-body  py-2 my-25">
                    <form class="form form-horizontal">
                        <div class="row">
                            <div class="col-md-6  mb-1">
                                <div class="form-group">
                                    <label class="col-form-label" for="Company Name">CMS Name</label>
                                    <input type="text" name="cms_name" class="form-control" placeholder="Enter CMS Name" />
                                </div>
                            </div>
                            <div class="col-md-6  mb-1">
                                <div class="form-group">
                                    <label class="col-form-label" for="Date Format">Date Format</label>
                                    <select name="date_format" class="form-control">
                                        <option value="" selected disabled>--Select Date Format--</option>
                                        <option value="d M, Y">d M, Y</option>
                                        <option value="d/m/y">d/m/y</option>
                                        <option value="Y-m-d">Y-m-d</option>
                                        <option value="Y-m-d H:i:s">Y-m-d H:i:s</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="col-form-label" for="Company favicon">Favicon</label>
                                <div class="d-flex">
                                    <a href="#" class="me-25">
                                        <img src="{{ asset('assets/images/avatar.png') }}" id="favicon-img" class="uploadedAvatar rounded me-50" alt="profile image"
                                            height="100" width="100">
                                    </a>
                                    <!-- upload and reset button -->
                                    <div class="d-flex align-items-end mt-75 ms-1">
                                        <div>
                                            <label for="favicon" class="btn btn-sm btn-primary mb-75 me-75 waves-effect waves-float waves-light">Upload</label>
                                            <input type="file" id="favicon" hidden="" accept="image/*">
                                            <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
                                        </div>
                                    </div>
                                </div>
                                @error('favicon')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="col-form-label" for="Company Logo">Logo</label>
                                <div class="d-flex">
                                    <a href="#" class="me-25">
                                        <img src="{{ asset('assets/images/avatar.png') }}" id="logo-img" class="uploadedAvatar rounded me-50" alt="profile image"
                                            height="100" width="100">
                                    </a>
                                    <!-- upload and reset button -->
                                    <div class="d-flex align-items-end mt-75 ms-1">
                                        <div>
                                            <label for="logo" class="btn btn-sm btn-primary mb-75 me-75 waves-effect waves-float waves-light">Upload</label>
                                            <input type="file" id="logo" hidden="" accept="image/*">
                                            <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
                                        </div>
                                    </div>
                                </div>
                                @error('logo')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 text-end">
                                <button type="reset" class="btn btn-primary me-1 waves-effect waves-float waves-light">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary waves-effect">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
