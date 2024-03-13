<form wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
    <x-input type="hidden" wire:model="form.business_id" />
    <div class="row mb-1">
        <div class="col-md-12">
            <x-input-label for="name" class="required" value="Name" />
            <x-input type="text" name="name" id="name"
                :class="$errors->has('form.name') ? 'error' : ''"
                placeholder="Enter name" wire:model="form.name" />
            @error('form.name')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-md-12">
            <x-input-label for="address" value="Street Address" />
            <x-textarea id="address" data-length="200" length="200" rows="3"
                :class="$errors->has('form.address') ? 'error char-textarea' : 'char-textarea'"
                placeholder="Enter street address" wire:model="form.address" />
            @error('form.address')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>
    <div class="row mb-1">
        <div class="col-md-6">
            <x-input-label for="city" class="required" value="City" />
            <x-input type="text" name="city" id="city" :class="$errors->has('form.city') ? 'error' : ''" placeholder="Enter city" wire:model="form.city" />
            @error('form.city')
                <x-input-error :message="$message" />
            @enderror
        </div>
        <div class="col-md-6">
            <span wire:ignore.>
                <x-input-label for="country" class="required" value="Country" />
                <x-select-input name="country_id" id="country-select"
                    :class="$errors->has('form.country_id') ? 'error select-two select2' : 'select-two select2'"
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
        <div class="col-md-12">
            <x-input-label for="postal_code" value="Postal Code" />
            <x-input type="number" name="postal_code" id="postal_code"
                :class="$errors->has('form.postal_code') ? 'error' : ''"
                placeholder="Enter postal code" wire:model="form.postal_code" />
            @error('form.postal_code')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="row mb-1">
        <div class="col-md-6">
            <x-input-label for="rate_per_hour" value="Rate Per Hour" />
            <x-input type="text" name="rate_per_hour" id="rate_per_hour"
                :class="$errors->has('form.rate_per_hour') ? 'error' : ''"
                placeholder="Enter rate per hour" wire:model="form.rate_per_hour" />
            @error('form.rate_per_hour')
                <x-input-error :message="$message" />
            @enderror
        </div>

        <div class="col-md-6">
            <span wire:ignore.>
                <x-input-label for="rate_unit" value="Rate Unit" />
                <x-select-input name="rate_unit" id="rate-unit-select"
                    :class="$errors->has('form.rate_unit') ? 'error select-two select2' : 'select-two select2'"
                    wire:model="form.rate_unit">
                    <option>Select</option>
                    @isset($rateUnits)
                        @foreach ($rateUnits as $rateUnit)
                            <option value="{{ $rateUnit?->code }}">{{ $rateUnit?->code }}</option>
                        @endforeach
                    @endisset
                </x-select-input>
            </span>
            @error('form.rate_unit')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="add-user">
        <div class="d-flex mb-1">
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="col-md-6">
                        <x-input-label for="first_name" value="Add User" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch form-check-success">
                            <x-input type="checkbox" class="form-check-input" id="add-user"
                                name="add_user" wire:click="form.add_user" wire:model="form.add_user" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="user-form d-none" id="user-form">
            <div class="row mb-1">
                <div class="col-md-2 pe-md-1">
                    <x-input-label for="first_name" value="First Name" />
                    <x-input type="text" id="first_name" placeholder="Enter first name"
                        wire:model="form.first_name.0" />
                </div>
                <div class="col-md-2 px-md-1">
                    <x-input-label for="last_name" value="Last Name" />
                    <x-input type="text" id="last_name" placeholder="Enter last name"
                        wire:model="form.last_name.0" />
                </div>
                <div class="col-md-3 px-md-1">
                    <x-input-label for="email" value="Email" />
                    <x-input type="text" id="email" placeholder="Enter email"
                        wire:model="form.email.0" />
                </div>
                <div class="col-md-2 px-md-1">
                    <x-input-label for="phone" value="Phone" />
                    <x-input type="text" id="phone" placeholder="Enter phone number"
                        wire:model="form.phone.0" />
                </div>
                <div class="col-md-2 px-md-1">
                    <x-input-label for="password" value="Password" />
                    <x-input type="text" id="password" placeholder="Enter password"
                        wire:model="form.password.0" />
                </div>
                <div class="col-md-1 ps-md-1">
                    <div class="row">
                        <div class="col-md-12">
                            <x-input-label for="action" value="Action" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <x-anchor-tag class="btn btn-icon btn-outline-primary"
                                href="javascript:void(0);" id="add-user-fields" wire:ignore.>
                                    <i data-feather="plus"></i>
                            </x-anchor-tag>
                        </div>
                    </div>
                </div>
            </div>

            <div id="user-fields"></div>
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
            $(document).on('change', '#add-user', function (event) {
                var $this = $(this);
                // Check if the checkbox is checked
                if ($this.is(':checked')) {
                    // Checkbox is checked, capture its value
                    $(this).prop('checked', true);
                    $this.val('on');
                    $('.user-form').removeClass('d-none');
                } else if (!$this.is(':checked')){
                    $(this).prop('checked', false);
                    $this.val('off')
                    $('.user-form').addClass('d-none');
                }
            });

            $(document).on('click', '#add-user-fields', function (event) {
                event.preventDefault();
                let url = "{{ route('dashboard.clients.add-user-fields') }}";
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (response) {
                        $('#user-fields').append(response.html);
                        Livewire.dispatch('feather-icons');
                    },
                    error: function (response) {}
                });
            });

            $('#country-select').on('change', function(e) {
                @this.set('form.country_id', $(this).val());
            });

            $('#rate-unit-select').on('change', function(e) {
                @this.set('form.rate_unit', $(this).val());
            });

            // Event delegation to handle dynamically added elements
            $(document).on('click', '.delete-user-fields', function () {
                // Find the parent row and remove it
                $(this).closest('.user-fields-row').remove();
            });
        });
    </script>
@endscript
