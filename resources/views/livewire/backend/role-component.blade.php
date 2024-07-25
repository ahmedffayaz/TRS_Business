@section('breadcrumbs', Breadcrumbs::render('roles'))
<div>
    <div class="row">
        @foreach ($roles as $role)
        @if($role->name  != "super-admin")
            <div class="col-xl-4 col-lg-6 col-md-6 h-100">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <span>Total {{ $role->users()->sessionBusiness()->count() }} users</span>
                            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                                @for ($i = 1; $i <= min(7, $role->users()->sessionBusiness()->count()); $i++)
                                <li class="avatar avatar-sm pull-up">
                                        <img class="rounded-circle" src="{{ asset('assets/images/avatar.png') }}" alt="Avatar" />
                                    </li>
                                    @endfor
                            </ul>
                        </div>
                        <div class="d-flex justify-content-between align-items-end mt-1 pt-25">
                            <div class="role-heading">
                                <h4 class="fw-bolder">{{ ucwords($role->name) }}</h4>
                                @can('edit_roles')
                                <a href="javascript:;" class="role-edit-modal" wire:click="edit('{{ $role->id }}')">
                                    <small class="fw-bolder">Edit Role</small>
                                </a>
                                @endcan
                                @can('view_roles')
                                <a href="javascript:;" class="role-edit-modal ms-2" wire:click="viewPermission('{{ $role->id }}')">
                                    <small class="fw-bolder">View Role</small>
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            <div class="col-xl-4 col-lg-6 col-md-6 h-100">
                <div class="card">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="d-flex align-items-end justify-content-center h-100">
                            <img src="{{ asset('assets/illustrations/faq-illustrations.svg') }}" class="img-fluid mt-2" alt="Image" width="85" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="card-body text-sm-end text-center ps-sm-0">
                            <a href="javascript:void(0)" aria-controls="table-hover" type="button" wire:click="openModal" class="stretched-link text-nowrap add-new-role">
                                <span class="btn btn-primary mb-1">Add New Role</span>
                            </a>
                            <p class="mb-0">Add role, if it does not exist</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($permission)
    <x-main-modal wireIgnoreSelf="wire:ignore.self"
    modalTitle="Permissions based on roles">
    @include('livewire.backend.show-role-Permission', compact('permissionList','roleName' ))
    </x-main-modal>
    @else
    <x-main-modal wireIgnoreSelf="wire:ignore.self">
        <div class="text-center mb-2">
            <h1 class="mb-1">{{ $form->isUpdate ? 'Update' : 'Add' }} Role</h1>
            <p>Set role permissions.</p>
        </div>
        <form class="row" wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
            <div class="col-12">
                <label class="form-label">Role Name</label>
                <input type="text" class="form-control  @error('form.title') is-invalid @enderror" wire:model="form.title" placeholder="Role Name" autofocus
                data-msg="Please enter role name" />
                @error('form.title')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-12  mt-75">
                <h4 class="mt-2 pt-50">Role Permissions</h4>
                <div>
                    <table class="table table-flush-spacing">
                        <tbody>
                            {{-- @php $allPermissions = getGroupPermissions(); @endphp --}}
                            @foreach ($permissionList as $group => $permissions)
                                <tr>
                                    <td class="text-nowrap fw-bolder">{{ ucwords($group) }}</td>
                                    <td>
                                        <div class="d-flex row">
                                            @foreach ($permissions as $permission)
                                                <div class="col-4 mb-1 form-check me-3 me-lg-5">
                                                    <input class="form-check-input" type="checkbox" id="{{ $permission->title }}" value="{{ $permission->title }}" wire:model="form.permissions"
                                                        {{ in_array(old('permissions', isset($form->permissions) && checkRoleHasPermission($form->permissions, $permission->title) ?? ''), [$permission->title]) ? 'checked' : '' ,}}>
                                                    <label class="form-check-label" for="{{ $permission->title }}"> {{ ucfirst($permission->title) }} </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Permission table -->
            </div>
            <div class="col-12 text-end mt-75">
                <button class="btn btn-primary me-1 waves-effect waves-float waves-light" tabindex="4" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $form->isUpdate ? __('Update Changes') : __('Save Changes') }}</span>
                    <span wire:loading>
                        <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                    </span>
                </button>
            </div>
        </form>
    </x-main-modal>
    @endif
</div>
