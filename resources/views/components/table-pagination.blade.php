@props(['limitPerPage' => null])
<div class="d-flex align-items-center ms-2">
    <span class="">Show</span>
    <select class="form-select w-auto" wire:model.live.debounce.500ms="{{ $limitPerPage }}">
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="20">20</option>
        <option value="25">25</option>
        <option value="30">30</option>
        <option value="35">35</option>
    </select>
    <span class="">entries</span>
</div>
