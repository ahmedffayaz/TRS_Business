<form wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
    <div class="row mb-1">
        <div class="col-md-6">
            <x-input-label for="name" class="required" value="Name" />
            <x-input type="text" name="name" id="name" :class="$errors->has('form.name') ? 'error' : ''"
                placeholder="Enter name" wire:model="form.name" />
            @error('form.name')
                <x-input-error :message="$message" />
            @enderror
        </div>
        <div class="col-md-6">
            <span wire:ignore.>
            <x-input-label for="roles" class="required" value="Roles" />
            <x-select-input name="roles" id="role-select" multiple
                :class="$errors->has('form.roles') ? 'error select2' : 'select2'"
                wire:model="form.roles">
                @isset($roles)
                    @foreach ($roles as $role)
                        <option value="{{ $role?->id }}">{{ $role?->title }}</option>
                    @endforeach
                @endisset
            </x-select-input>
            </span>
            @error('form.roles')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-md-12">
            <x-input-label class="required" for="address" value="Address" />
            <x-textarea name="address" id="address"
                :class="$errors->has('form.address') ? 'error char-textarea' : 'char-textarea'"
                placeholder="Enter address" wire:model="form.address"
                data-length="200" length="200" rows="3" />
            @error('form.address')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-md-6 pe-md-1">
            <x-input-label for="city" class="required" value="City" />
            <x-input type="text" name="city" id="city" :class="$errors->has('form.city') ? 'error' : ''"
                placeholder="Enter city" wire:model="form.city" />
            @error('form.city')
                <x-input-error :message="$message" />
            @enderror
        </div>
        <div class="col-md-6 ps-md-1">
            <span wire:ignore.>
            <x-input-label for="country" class="required" value="Country" />
            <x-select-input name="country_id" id="country-select"
                :class="$errors->has('form.country_id') ? 'error select2' : 'select2'"
                wire:model="form.country_id">
                <option>Select</option>
                @isset($countries)
                    @foreach ($countries as $country)
                        <option value="{{ $country?->id }}">{{ $country?->name }}</option>
                    @endforeach
                @endisset
            </x-select-input>
            </span>
            @error('form.country_id')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-md-6 pe-md-1">
            <x-input-label class="required" for="postal_code" value="Postal Code" />
            <x-input type="number" name="postal_code" id="postal_code"
                :class="$errors->has('form.postal_code') ? 'error' : ''"
                placeholder="Enter postal code" wire:model="form.postal_code" />
            @error('form.postal_code')
                <x-input-error :message="$message" />
            @enderror
        </div>

        <div class="col-md-6 ps-md-1">
            <x-input-label for="logo" value="Business Logo" />
            <x-input type="file" name="logo" id="logo"
                :class="$errors->has('form.logo') ? 'error' : ''"
                placeholder="Enter postal code" wire:model="form.logo" />
            @error('form.logo')
                <x-input-error :message="$message" />
            @enderror
            @if ($form->logo)
                <img src="{{ $form->logo->temporaryUrl() }}" class="mt-2" width="180px" />
            @endif
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-md-6 pe-md-1">
            <x-input-label class="required" for="invoice_prefix" value="Invoice Prefix" />
            <x-input type="text" name="invoice_prefix" id="invoice_prefix" :class="$errors->has('form.invoice_prefix') ? 'error' : ''" placeholder="Enter invoice prefix"
                wire:model="form.invoice_prefix" />
            @error('form.invoice_prefix')
                <x-input-error :message="$message" />
            @enderror
        </div>
        <div class="col-md-6 ps-md-1">
            <x-input-label class="required" for="invoice_serial" value="Invoice Serial" />
            <x-input type="text" name="invoice_serial" id="invoice_serial" :class="$errors->has('form.invoice_serial') ? 'error' : ''" placeholder="Enter invoice serial"
                wire:model="form.invoice_serial" />
            @error('form.invoice_serial')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-12 text-end">
            <x-primary-button type="submit">Submit</x-primary-button>
        </div>
    </div>
</form>

@script
    <script>
        $(document).ready(function () {

            Livewire.on('roles-select', (data) => {
                var $select = $('#role-select');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formRoles).trigger('change');
            });

            $('#role-select').on('change', function(e) {
                @this.set('form.roles', $(this).val());
            });

            $('#country-select').on('change', function(e) {
                @this.set('form.country_id', $(this).val());
            });
        })
    </script>
@endscript
