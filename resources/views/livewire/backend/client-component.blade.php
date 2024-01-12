<div class="card">
    <div class="card-header">
        <h4 class="card-title">Clients</h4>
        <div>
            <a href="javascript:void(0);" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button" wire:click="openOffcanvas">Add Client</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-4 col-sm-12">
                <div class="input-group input-group-merge">
                    <span class="input-group-text" wire:ignore id="basic-addon-search2"><i data-feather="search"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search..." aria-label="Search..."
                        aria-describedby="basic-addon-search2" />
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <h4 class="alert-heading d-flex align-items-center">
                    <span wire:ignore><i data-feather="alert-triangle" class="me-50"></i></span>
                    Error
                </h4>
                <div class="alert-body">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="table-responsive overflow-visible">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Under Company</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($clients)
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{ $client?->name }}</td>
                                <td>
                                    {{ $client?->country?->name }}
                                </td>
                                <td>
                                    {{ $client?->company?->name }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                            <span wire:ignore><i data-feather="more-vertical"></i></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit('{{ $client?->id }}')">
                                                <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                <span>Edit</span>
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation('{{ $client?->id }}')">
                                                <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endisset
                </tbody>
            </table>
            {{ $clients->links('components.pagination') }}
        </div>
    </div>
    <x-modal-offcanvas wireIgnoreSelf="wire:ignore.self">
        <form class="modal-content pt-0" wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
            <button type="button" class="btn-close" wire:click="closeOffcanvas">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="modalOffcanvasLabel">{{ $form->isUpdate ? 'Edit Company' : 'Add Company' }}</h5>
            </div>
            <div class="modal-body flex-grow-1">
                <div class="mb-1">
                    <x-input-label for="company" class="required" value="Company" />
                    <x-select-input name="company_id" id="company" :class="$errors->has('form.company_id') ? 'error select-two' : 'select-two'" wire:model="form.company_id">
                        <option>Select</option>
                        @isset($companies)
                            @foreach ($companies as $company)
                                <option value="{{ $company?->id }}">{{ $company?->name }}</option>
                            @endforeach
                        @endisset
                    </x-select-input>
                    @error('form.company_id')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="name" class="required" value="Name" />
                    <x-input type="text" name="name" id="name" :class="$errors->has('form.name') ? 'error' : ''" placeholder="Enter name" wire:model="form.name" />
                    @error('form.name')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="street_address" value="Street Address" />
                    <x-input type="text" name="street_address" id="street_address" :class="$errors->has('form.street_address') ? 'error' : ''" placeholder="Enter street address" wire:model="form.street_address" />
                    @error('form.street_address')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="city" class="required" value="City" />
                    <x-input type="text" name="city" id="city" :class="$errors->has('form.city') ? 'error' : ''" placeholder="Enter city" wire:model="form.city" />
                    @error('form.city')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="country" class="required" value="Country" />
                    <x-select-input name="country_id" id="country" :class="$errors->has('form.country_id') ? 'error select-two' : 'select-two'" wire:model="form.country_id">
                        <option>Select</option>
                        @isset($countries)
                            @foreach ($countries as $country)
                                <option value="{{ $country?->id }}">{{ $country?->name }}</option>
                            @endforeach
                        @endisset
                    </x-select-input>
                    @error('form.country_id')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="postal_code" value="Postal Code" />
                    <x-input type="number" name="postal_code" id="postal_code" :class="$errors->has('form.postal_code') ? 'error' : ''" placeholder="Enter postal code" wire:model="form.postal_code" />
                    @error('form.postal_code')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="rate_per_hour" value="Rate Per Hour" />
                    <x-input type="text" name="rate_per_hour" id="rate_per_hour" :class="$errors->has('form.rate_per_hour') ? 'error' : ''" placeholder="Enter rate per hour"
                        wire:model="form.rate_per_hour" />
                    @error('form.rate_per_hour')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="rate_unit" value="Rate Unit" />
                    <x-select-input name="rate_unit" id="rate_unit" :class="$errors->has('form.rate_unit') ? 'error select-two' : 'select-two'" wire:model="form.rate_unit">
                        <option>Select</option>
                        @isset($rateUnits)
                            @foreach ($rateUnits as $rateUnit)
                                <option value="{{ $rateUnit?->code }}">{{ $rateUnit?->code }}</option>
                            @endforeach
                        @endisset
                    </x-select-input>
                    @error('form.rate_unit')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mt-2">
                    <x-primary-button type="submit" class="me-1">Submit</x-primary-button>
                    <x-button type="reset" class="btn btn-outline-secondary" wire:click="closeOffcanvas">Cancel</x-button>
                </div>
            </div>
        </form>
    </x-modal-offcanvas>
</div>
