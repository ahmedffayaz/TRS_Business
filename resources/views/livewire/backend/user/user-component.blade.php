@section('breadcrumbs', Breadcrumbs::render('users'))
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Users</h4>
        @can('add_users')
            <div>
                <x-anchor-tag href="javascript:void(0);" class="btn btn-primary" tabindex="0"
                    aria-controls="table-hover" type="button" wire:click="openMainModal">Add User</x-anchor-tag>
            </div>
        @endcan
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
            <div class="col-md-8 col-sm-12 text-end">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <x-anchor-tag href="javascript:void(0)" class="btn btn-sm btn-outline-primary" wire:click="$set('userTypes', 'total')">Total <span class="badge rounded-pill bg-light-primary">{{ $totalUsers }}</span></x-anchor-tag>
                    <x-anchor-tag href="javascript:void(0)" class="btn btn-sm btn-outline-primary" wire:click="$set('userTypes', 'active')">Active <span class="badge rounded-pill bg-light-primary">{{ $activeUsers }}</span></x-anchor-tag>
                    <x-anchor-tag href="javascript:void(0)" class="btn btn-sm btn-outline-primary" wire:click="$set('userTypes', 'archived')">Archived <span class="badge rounded-pill bg-light-primary">{{ $archivedUsers }}</span></x-anchor-tag>
                </div>
            </div>
        </div>

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
                                        <div class="d-flex flex-column">
                                            <x-anchor-tag href="{{ route('dashboard.users.profile', $user->id) }}" class="user_name text-truncate text-body">
                                                <span class="fw-bolder">{{ $user?->getFullName() }}</span>
                                            </x-anchor-tag>
                                            <small class="emp_post text-muted">{{ $user?->email }}</small>
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
                                    @if(count($user->roles) > 0)
                                        @foreach ($user->roles as $role)
                                            <span class="badge rounded-pill bg-secondary">{{ $role->title }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown position-static">
                                        @can('edit_users', 'delete_users')
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('edit_users')
                                                    <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="edit('{{ $user?->id }}')">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Edit</span>
                                                    </x-anchor-tag>
                                                    <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="toggleStatus('{{ $user?->id }}')">
                                                        @if ($user?->is_active)
                                                            <span wire:ignore><i data-feather="arrow-down" class="me-50"></i></span>
                                                            <span>Deactivate</span>
                                                        @else
                                                            <span wire:ignore><i data-feather="arrow-up" class="me-50"></i></span>
                                                            <span>Activate</span>
                                                        @endif
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('delete_users')
                                                    @if (empty($user->deleted_at))
                                                        <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation('{{ $user?->id }}')">
                                                            <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                                            <span>Delete</span>
                                                        </x-anchor-tag>
                                                    @endif
                                                @endcan
                                            </div>
                                        @endcan
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
    @can('add_users')
        <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal">
            <div class="text-center mb-2">
                <h1 class="mb-1">{{ $form->isUpdate ? 'Edit' : 'Add' }} User</h1>
            </div>
            <form wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
                <div class="row">
                    <div class="col-md-6 mb-1">
                        <x-input-label for="designation" class="required" value="Designation" />
                        <x-input type="text" name="designation" id="designation" :class="$errors->has('form.designation') ? 'error' : ''" placeholder="Enter designation" wire:model="form.designation" />
                        @error('form.designation')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="client" class="required" value="Client" />
                        <span wire:ignore.>
                            <x-select-input name="client_id" id="client" :class="$errors->has('form.client_id') ? 'error select2' : 'select2'" wire:model="form.client_id">
                                <option value="" selected>--Select Client--</option>
                                @isset($clients)
                                    @foreach ($clients as $client)
                                        <option value="{{ $client?->id }}">{{ $client?->name }}</option>
                                    @endforeach
                                @endisset
                            </x-select-input>
                        </span>
                        @error('form.client_id')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="first_name" class="required" value="First Name" />
                        <x-input type="text" name="first_name" id="first_name" :class="$errors->has('form.first_name') ? 'error' : ''" placeholder="Enter first name" wire:model="form.first_name"
                            autocomplete="given-name" />
                        @error('form.first_name')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="last_name" class="required" value="Last Name" />
                        <x-input type="text" name="last_name" id="last_name" :class="$errors->has('form.last_name') ? 'error' : ''" placeholder="Enter last name" wire:model="form.last_name"
                            autocomplete="family-name" />
                        @error('form.last_name')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="email" class="required" value="Email" />
                        <x-input type="email" name="email" id="email" :class="$errors->has('form.email') ? 'error' : ''" placeholder="Enter email" wire:model="form.email" autocomplete="username" />
                        @error('form.email')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="alternative_email" value="Alternative Email" />
                        <x-input type="email" name="alternative_email" id="alternative_email" :class="$errors->has('form.alternative_email') ? 'error' : ''" placeholder="Enter alternative email"
                            wire:model="form.alternative_email" autocomplete="username" />
                        @error('form.alternative_email')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="salary" value="Salary" />
                        <x-input type="number" name="salary" id="salary" :class="$errors->has('form.salary') ? 'error' : ''" placeholder="Enter Salary" wire:model="form.salary" />
                        @error('form.salary')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="currency" value="Currency" />
                        <span wire:ignore.>
                            <x-select-input name="currency" id="currency" :class="$errors->has('form.currency') ? 'error select2' : 'select2'" wire:model="form.currency">
                                @isset($currencies)
                                <option value="" selected>--Select Currency--</option>
                                @foreach ($currencies as $currency)
                                        <option value="{{ $currency?->code }}">{{ $currency?->code }}</option>
                                    @endforeach
                                @endisset
                            </x-select-input>
                        </span>
                        @error('form.currency')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="password" class="{{ !$form->isUpdate ? 'required' : '' }}" value="Password" />
                            <x-input type="password" name="password" id="password" :class="$errors->has('form.password') ? 'error' : ''" placeholder="Enter password" wire:model="form.password"
                                autocomplete="new-password" />
                            @error('form.password')
                                <x-input-error :message="$message" />
                            @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="password_confirmation" class="{{ !$form->isUpdate ? 'required' : '' }}" value="Confirm Password" />
                            <x-input type="password" name="password_confirmation" id="password_confirmation" :class="$errors->has('form.password_confirmation') ? 'error' : ''" placeholder="Re-enter password"
                                wire:model="form.password_confirmation" autocomplete="new-password" />
                            @error('form.password_confirmation')
                                <x-input-error :message="$message" />
                            @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="phone" class="required" value="Phone Number" />
                        <x-input type="text" name="phone" id="phone" :class="$errors->has('form.phone') ? 'error' : ''" placeholder="Enter phone number" wire:model="form.phone" />
                        @error('form.phone')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="alternative_number" value="Alternative Phone Number" />
                        <x-input type="text" name="alternative_number" id="alternative_number" :class="$errors->has('form.alternative_number') ? 'error' : ''" placeholder="Enter alternative phone number"
                            wire:model="form.alternative_number" />
                        @error('form.alternative_number')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="roles" class="required" value="roles" />
                        <span wire:ignore.>
                            <x-select-input name="roles" id="roles" :class="$errors->has('form.roles') ? 'error select2' : 'select2'" multiple wire:model="form.roles">
                                @isset($roles)
                                <option value="" selected>--Select Roles--</option>
                                @foreach ($roles as $role)
                                        <option value="{{ $role?->id }}">{{ $role?->name }}</option>
                                    @endforeach
                                @endisset
                            </x-select-input>
                        </span>
                        @error('form.roles')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-6 mb-1">
                        <x-input-label for="status" class="required" value="Status" />
                        <span wire:ignore.>
                            <x-select-input name="is_active" id="status" :class="$errors->has('form.is_active') ? 'error select2' : 'select2'" wire:model="form.is_active">
                                @isset($userStatuses)
                                <option value="" selected>--Select Status--</option>
                                @foreach ($userStatuses as $userStatus)
                                        <option value="{{ $userStatus?->value }}">{{ ucfirst(strtolower($userStatus?->name)) }}
                                        </option>
                                    @endforeach
                                @endisset
                            </x-select-input>
                        </span>
                        @error('form.is_active')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <x-input-label for="address" class="required" value="Address" />
                        <x-textarea name="address" id="address" :class="$errors->has('form.address') ? 'error char-textarea' : 'char-textarea'" data-length="200" length="200" rows="3" placeholder="Enter address"
                            autocomplete="on" wire:model="form.address"></x-textarea>
                        @error('form.address')
                            <x-input-error :message="$message" />
                        @enderror
                    </div>
                    <div class="col-md-12 text-center">
                        <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $form->isUpdate ? 'Update' : 'Add' }}</span>
                            <span wire:loading>
                                <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                            </span>
                        </x-button>
                    </div>
                </div>
            </form>
        </x-main-modal>
    @endcan
</div>

@script
    <script>
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });

            // Initialize select2
            Livewire.dispatch('select-container');
            Livewire.on('select-client', (data) => {
                var $select = $('#client');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formClient).trigger('change');
            });

            Livewire.on('select-currency', (data) => {
                var $select = $('#currency');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formCurrency).trigger('change');
            });

            Livewire.on('select-status', (data) => {
                var $select = $('#status');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formStatus).trigger('change');
            });

            Livewire.on('select-roles', (data) => {
                var $select = $('#roles');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formRoles).trigger('change');
            });

            $('#client').on('change', function(e) {
                @this.set('form.client_id', $(this).val());
            });

            $('#currency').on('change', function(e) {
                @this.set('form.currency', $(this).val());
            });

            $('#status').on('change', function(e) {
                @this.set('form.is_active', $(this).val());
            });

            $('#roles').on('change', function(e) {
                @this.set('form.roles', $(this).val());
            });
        })
    </script>
@endscript
