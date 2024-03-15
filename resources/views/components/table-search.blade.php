@props(['total', 'active', 'archived'])
<div class="row mb-2">
    <div class="col-md-4 col-sm-12">
        <div class="input-group input-group-merge">
            <span class="input-group-text" id="basic-addon-search2" wire:ignore><i data-feather="search"></i></span>
            <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search..."
                aria-label="Search..." aria-describedby="basic-addon-search2" />
        </div>
    </div>
    <div class="col-md-2 col-sm-12">
        <select class="form-select" wire:model.live.debounce.500ms="limitPerPage">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="25">25</option>
            <option value="30">30</option>
            <option value="35">35</option>
        </select>
    </div>
    <div class="col-md-6 col-sm-12 text-end">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <x-anchor-tag href="javascript:void(0)" class="btn btn-sm btn-outline-primary"
                wire:click="$set('userTypes', 'total')">Total <span
                    class="badge rounded-pill bg-light-primary">{{ $total }}</span></x-anchor-tag>
            <x-anchor-tag href="javascript:void(0)" class="btn btn-sm btn-outline-primary"
                wire:click="$set('userTypes', 'active')">Active <span
                    class="badge rounded-pill bg-light-primary">{{ $active }}</span></x-anchor-tag>
            <x-anchor-tag href="javascript:void(0)" class="btn btn-sm btn-outline-primary"
                wire:click="$set('userTypes', 'archived')">Archived <span
                    class="badge rounded-pill bg-light-primary">{{ $archived }}</span></x-anchor-tag>
        </div>
    </div>
</div>
