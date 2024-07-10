@section('breadcrumbs', Breadcrumbs::render('system_settings'))
<section id="basic-horizontal-layouts">
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">System Setting</h4>
                </div>
                <div class="card-body  py-2 my-25">
                    <form class="form form-horizontal" wire:submit.prevent="submit">
                        <div class="row">
                            <div class="col-md-6  mb-1">
                                <div class="form-group">
                                    <label class="col-form-label" for="Company Name">CMS Name</label>
                                    <input type="text" name="cms_name" class="form-control  @error('form.cms_name') is-invalid @enderror" wire:model="form.cms_name" placeholder="Enter CMS Name" />
                                    @error('form.cms_name')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6  mb-1">
                                <div class="form-group">
                                    <label class="col-form-label" for="Date Format">Date Format</label>
                                    <select name="date_format" class="form-control  @error('form.date_format') is-invalid @enderror" wire:model="form.date_format">
                                        <option value="" selected disabled>--Select Date Format--</option>
                                        <option value="d M, Y">d M, Y</option>
                                        <option value="d/m/y">d/m/y</option>
                                        <option value="Y-m-d">Y-m-d</option>
                                        <option value="Y-m-d H:i:s">Y-m-d H:i:s</option>
                                    </select>
                                    @error('form.date_format')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="col-form-label" for="Company favicon">Favicon</label>
                                <div class="d-flex">
                                    <a href="#" class="me-25">
                                        <img src="{{ $form?->favicon
                                                 ? $form?->favicon?->temporaryUrl()
                                                : (isset($cmsFavicon) && $cmsFavicon ? asset('storage/cms/images/' . $cmsFavicon) : asset($defaultFavicon)) }}" wire:model="form.favicon"  id="favicon-img" class="uploadedAvatar rounded me-50" alt="profile image"
                                            height="100" width="100">
                                    </a>
                                    <div class="d-flex align-items-end mt-75 ms-1">
                                        <div>
                                            <label for="favicon" class="btn btn-sm btn-primary mb-75 me-75 waves-effect waves-float waves-light">Upload</label>
                                            <input type="file" id="favicon" hidden="" accept="image/*" wire:model="form.favicon">
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
                                        <img src="{{ $form?->logoFile
                                            ? $form?->logoFile?->temporaryUrl()
                                           : (isset($cmsLogo) && $cmsLogo ? asset('storage/cms/images/' . $cmsLogo) : asset($defaultLogo)) }}" wire:model="form.logoFile"  id="logo-img" class="uploadedAvatar rounded me-50" alt="profile image"
                                            height="100" width="100">
                                    </a>
                                    <div class="d-flex align-items-end mt-75 ms-1">
                                        <div>
                                            <label for="logo" class="btn btn-sm btn-primary mb-75 me-75 waves-effect waves-float waves-light">Upload</label>
                                            <input type="file" id="logo" hidden="" accept="image/*" wire:model="form.logoFile">
                                            <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
                                        </div>
                                    </div>
                                </div>
                                @error('logo')
                                    <small class="text-danger mt-2">{{ $message }}</small>
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
