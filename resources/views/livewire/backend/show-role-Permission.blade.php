
@if (!isset($permissionList) || count($permissionList) === 0)
    <h4 class="text-center">No Permission Found</h4>
@else
<div class="text-center mb-2">
    <h1 class="mb-1"> Role Name: {{ ucwords($roleName) }}</h1>
    <p>Viewing role permissions.</p>
</div>
<div class="row">
    @foreach ($permissionList as $group => $permissions)
     <tr>
        <td class="text-nowrap fw-bolder">{{ ucwords($group) }}</td>
        <td>
            <div class="d-flex row" wire:ignore>
                @foreach ($permissions as $permission)
                    <div class="col-4 mb-1 me-3 me-lg-5">
                     <p class="text-center">{{ $permission->title }}</p>
                    </div>
                @endforeach
            </div>
        </td>
    </tr>
@endforeach
</div>
@endif
