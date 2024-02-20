<div class="row mb-1 user-fields-row">
    <div class="col-md-2 pe-md-1">
        <x-input-label for="first_name" value="First Name" />
        <x-input type="text" id="first_name" :class="$errors->has('form.first_name.*') ? 'error' : ''" placeholder="Enter first name"
            wire:model="form.first_name" />
    </div>
    <div class="col-md-2 px-md-1">
        <x-input-label for="last_name" value="Last Name" />
        <x-input type="text" id="last_name" :class="$errors->has('form.last_name.*') ? 'error' : ''" placeholder="Enter last name"
            wire:model="form.last_name" />
    </div>
    <div class="col-md-3 px-md-1">
        <x-input-label for="email" value="Email" />
        <x-input type="text" id="email" :class="$errors->has('form.email.*') ? 'error' : ''" placeholder="Enter email"
            wire:model="form.email" />
    </div>
    <div class="col-md-2 px-md-1">
        <x-input-label for="phone" value="Phone" />
        <x-input type="text" id="phone" :class="$errors->has('form.phone.*') ? 'error' : ''" placeholder="Enter phone number"
            wire:model="form.phone" />
    </div>
    <div class="col-md-2 px-md-1">
        <x-input-label for="password" value="Password" />
        <x-input type="text" id="password" :class="$errors->has('form.password.*') ? 'error' : ''" placeholder="Enter password"
            wire:model="form.password" />
    </div>

    <div class="col-md-1 ps-md-1">
        <div class="row">
            <div class="col-md-12">
                <x-input-label for="action" value="Action" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <x-anchor-tag class="btn btn-icon btn-outline-danger delete-user-fields" href="javascript:void(0);">
                    <span wire.ignore>
                        <i data-feather='trash-2'></i>
                    </span>
                </x-anchor-tag>
            </div>
        </div>
    </div>
</div>
