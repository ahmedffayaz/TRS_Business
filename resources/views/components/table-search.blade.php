@props(['dataCounter' => []])
<div class="row mb-2">
    @if (!empty($dataCounter))
        <div class="col-md-8 col-sm-12">
            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                @foreach($dataCounter as $key => $value)
                    <x-anchor-tag href="javascript:void(0)" class="btn btn-sm btn-outline-primary"
                        wire:click="$set('dataCountType', '{{ $key }}')">{{ $key }} <span
                        class="badge rounded-pill bg-light-primary">{{ $value }}</span>
                    </x-anchor-tag>
                @endforeach
            </div>
        </div>
    @endif
    <div class="col-md-4 col-sm-12">
        <div class="input-group input-group-merge">
            <span class="input-group-text" id="basic-addon-search2" wire:ignore><i data-feather="search"></i></span>
            <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search..."
                aria-label="Search..." aria-describedby="basic-addon-search2" />
        </div>
    </div>
</div>
