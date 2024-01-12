<div>
    <div class="d-flex">
        <a href="#" class="me-25">
            <img src="{{ $avatar }}" wire:model="avatar" id="account-upload-img" class="uploadedAvatar rounded me-50" alt="profile image"
                height="100" width="100">
        </a>
        <!-- upload and reset button -->
        <div class="d-flex align-items-end mt-75 ms-1">
            <div>
                <label for="account-upload" class="btn btn-sm btn-primary mb-75 me-75 waves-effect waves-float waves-light">Upload</label>
                <input type="file" wire:model="avatarFile"  wire:change="uploadAvatarFile"  id="account-upload" hidden="" accept="image/*">
                <p class="mb-0">Allowed file types: png, jpg, jpeg.</p>
            </div>
        </div>
        <!--/ upload and reset button -->
    </div>
</div>
