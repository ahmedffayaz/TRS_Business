<div class="card">
    <div class="card-header">
        <h4 class="card-title">Employees</h4>
        <div>
            <a href="javascript:void(0);" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button" wire:click="openOffcanvas">Add Employee</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-4 col-sm-12">
                <div class="input-group input-group-merge">
                    <span class="input-group-text" id="basic-addon-search2" wire:ignore><i data-feather="search"></i></span>
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
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($users)
                        @foreach ($users as $user)
                            <tr>
                                <td class="sorting_1">
                                    <div class="d-flex justify-content-left align-items-center">
                                        <div class="avatar-wrapper">
                                            <div class="avatar  me-1"><img src="{{ $user?->avatar }}" alt="Avatar" height="32" width="32"></div>
                                        </div>
                                        <div class="d-flex flex-column"><a href="app-user-view-account.html" class="user_name text-truncate text-body"><span
                                                    class="fw-bolder">{{ $user?->getFullName() }}</span></a><small class="emp_post text-muted">{{ $user?->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($user?->is_active)
                                        <span class="badge rounded-pill badge-light-success">Active</span>
                                    @else
                                        <span class="badge rounded-pill badge-light-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Admin</span>
                                </td>
                                <td>
                                    <div class="dropdown position-static">
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                            <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="edit('{{ $user?->id }}')">
                                                <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                <span>Edit</span>
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="toggleStatus('{{ $user?->id }}')">
                                                @if ($user?->is_active)
                                                    <span wire:ignore><i data-feather="arrow-down" class="me-50"></i></span>
                                                    <span>Deactivate</span>
                                                @else
                                                    <span wire:ignore><i data-feather="arrow-up" class="me-50"></i></span>
                                                    <span>Activate</span>
                                                @endif
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation('{{ $user?->id }}')">
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
            {{ $users->links('components.pagination') }}
        </div>
    </div>
    <x-modal-offcanvas wireIgnoreSelf="wire:ignore.self">
        <form class="add-new-user modal-content pt-0" wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
            <button type="button" class="btn-close" wire:click="closeOffcanvas">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title" id="modalOffcanvasLabel">{{ $form->isUpdate ? 'Edit Employee' : 'Add Employee' }}</h5>
            </div>
            <div class="modal-body flex-grow-1">
                <div class="mb-1">
                    <x-input-label for="designation" class="required" value="Designation" />
                    <x-input type="text" name="designation" id="designation" :class="$errors->has('form.designation') ? 'error' : ''" placeholder="Enter designation" wire:model="form.designation" />
                    @error('form.designation')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="company" class="required" value="Company" />
                    <x-select-input name="company_id" id="company" :class="$errors->has('form.company_id') ? 'error select-two' : 'select-two'" wire:model="form.company_id">
                        <option>Select Company</option>
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
                    <x-input-label for="first_name" class="required" value="First Name" />
                    <x-input type="text" name="first_name" id="first_name" :class="$errors->has('form.first_name') ? 'error' : ''" placeholder="Enter first name" wire:model="form.first_name"
                        autocomplete="given-name" />
                    @error('form.first_name')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="last_name" class="required" value="Last Name" />
                    <x-input type="text" name="last_name" id="last_name" :class="$errors->has('form.last_name') ? 'error' : ''" placeholder="Enter last name" wire:model="form.last_name"
                        autocomplete="family-name" />
                    @error('form.last_name')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="email" class="required" value="Email" />
                    <x-input type="email" name="email" id="email" :class="$errors->has('form.email') ? 'error' : ''" placeholder="Enter email" wire:model="form.email" autocomplete="username" />
                    @error('form.email')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="alternative_email" value="Alternative Email" />
                    <x-input type="email" name="alternative_email" id="alternative_email" :class="$errors->has('form.alternative_email') ? 'error' : ''" placeholder="Enter alternative email"
                        wire:model="form.alternative_email" autocomplete="username" />
                    @error('form.alternative_email')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="salary" value="Salary" />
                    <x-input type="number" name="salary" id="salary" :class="$errors->has('form.salary') ? 'error' : ''" placeholder="Enter Salary" wire:model="form.salary" />
                    @error('form.salary')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="currency" value="Currency" />
                    <x-select-input name="currency" id="currency" :class="$errors->has('form.currency') ? 'error select-two' : 'select-two'" wire:model="form.currency">
                        <option>Select</option>
                        @isset($currencies)
                            @foreach ($currencies as $currency)
                                <option value="{{ $currency?->code }}">{{ $currency?->code }}</option>
                            @endforeach
                        @endisset
                    </x-select-input>
                    @error('form.currency')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="password" class="{{ !$form->isUpdate ? 'required' : '' }}" value="Password" />
                    <x-input type="password" name="password" id="password" :class="$errors->has('form.password') ? 'error' : ''" placeholder="Enter password" wire:model="form.password"
                        autocomplete="new-password" />
                    @error('form.password')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="password_confirmation" class="{{ !$form->isUpdate ? 'required' : '' }}" value="Confirm Password" />
                    <x-input type="password" name="password_confirmation" id="password_confirmation" :class="$errors->has('form.password_confirmation') ? 'error' : ''" placeholder="Re-enter password"
                        wire:model="form.password_confirmation" autocomplete="new-password" />
                    @error('form.password_confirmation')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="address" class="required" value="Address" />
                    <x-textarea name="address" id="address" :class="$errors->has('form.address') ? 'error char-textarea' : 'char-textarea'" data-length="200" length="200" rows="3" placeholder="Enter address"
                        autocomplete="on" wire:model="form.address"></x-textarea>
                    @error('form.address')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="phone" class="required" value="Phone Number" />
                    <x-input type="text" name="phone" id="phone" :class="$errors->has('form.phone') ? 'error' : ''" placeholder="Enter phone number" wire:model="form.phone" />
                    @error('form.phone')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="alternative_number" value="Alternative Phone Number" />
                    <x-input type="text" name="alternative_number" id="alternative_number" :class="$errors->has('form.alternative_number') ? 'error' : ''" placeholder="Enter alternative phone number"
                        wire:model="form.alternative_number" />
                    @error('form.alternative_number')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <div class="mb-1">
                    <x-input-label for="status" class="required" value="Status" />
                    <x-select-input name="is_active" id="status" class="select-two" :class="$errors->has('form.is_active') ? 'error' : ''" wire:model="form.is_active">
                        <option>Select</option>
                        @isset($userStatuses)
                            @foreach ($userStatuses as $userStatus)
                                <option value="{{ $userStatus?->value }}">{{ ucfirst(strtolower($userStatus?->name)) }}
                                </option>
                            @endforeach
                        @endisset
                    </x-select-input>
                    @error('form.is_active')
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
