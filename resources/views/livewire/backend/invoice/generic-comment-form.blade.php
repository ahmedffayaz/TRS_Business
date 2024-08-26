
<div class="row mb-1">
    <div class="col-md-5">
        <x-input-label for="description" class="required" value="Description" />
        <x-input type="text" id="description-{{ $key }}" placeholder="Enter description"
            :class="$errors->has('invoiceForm.generic_comments.{{ $key }}.description') ? 'error' : ''"
            wire:model="invoiceForm.generic_comments.{{ $key }}.description" required />
        @error('invoiceForm.generic_comments.{{ $key }}.description')
            <x-input-error :message="$message" />
        @enderror
    </div>

    <div class="col-md-2">
        <x-input-label for="quantity" value="Qty" />
        <x-input type="number" :class="$errors->has('invoiceForm.generic_comments.{{ $key }}.quantity')
            ? 'error quantity' : 'quantity'" placeholder="Quantity" id="quantity-{{ $key }}"
            wire:model="invoiceForm.generic_comments.{{ $key }}.quantity"
            data-msg="Please enter quantity" min="1" autofocus />
        @error('invoiceForm.generic_comments.{{ $key }}.quantity')
            <x-input-error :message="$message" />
        @enderror
    </div>

    <div class="col-md-2">
        <x-input-label for="rate" value="Rate" />
        <x-input type="number" :class="$errors->has('invoiceForm.generic_comments.{{ $key }}.rate')
            ? 'error rate' : 'rate'" placeholder="Rate" id="rate-{{ $key }}"
            wire:model="invoiceForm.generic_comments.{{ $key }}.rate"
            data-msg="Please enter rate" min="1" autofocus />
        @error('invoiceForm.generic_comments.{{ $key }}.rate')
            <x-input-error :message="$message" />
        @enderror
    </div>

    <div class="col-md-2">
        <x-input-label for="amount" class="required" value="Amount" />
        <x-input type="number" wire:ignore :class="$errors->has('invoiceForm.generic_comments.{{ $key }}.amount')
            ? 'error generic-amount' : 'generic-amount'" placeholder="Amount" id="{{ $key }}" data-unit="{{ isset($tasks[0]) ? $tasks[0]->project?->currency : null }}"
            wire:model="invoiceForm.generic_comments.{{ $key }}.amount" data-generic-amount=""
            data-msg="Please enter amount" min="1" autofocus required />
        @error('invoiceForm.generic_comments.{{ $key }}.amount')
            <x-input-error :message="$message" />
        @enderror
    </div>

    <div class="col-md-1">
        <x-anchor-tag class="btn btn-icon btn-outline-danger delete-user-fields delete-comment" href="javascript:void(0);"
            wire:click="removeGenericCommentsFields({{ $key }})">
            <span wire.ignore>
                <i data-feather="trash-2"></i>
            </span>
        </x-anchor-tag>
    </div>
</div>

