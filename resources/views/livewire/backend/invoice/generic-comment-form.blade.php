@php $key = $key +1; @endphp
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
        <x-input type="number" :class="$errors->has('invoiceForm.generic_comments.{{ $key }}.amount')
            ? 'error amount' : 'amount'" placeholder="Amount" id="{{ $key }}"
            wire:model="invoiceForm.generic_comments.{{ $key }}.amount"
            data-msg="Please enter amount" min="1" autofocus required />
        @error('invoiceForm.generic_comments.{{ $key }}.amount')
            <x-input-error :message="$message" />
        @enderror
    </div>

    <div class="col-md-1">
        <x-anchor-tag class="btn btn-icon btn-outline-danger delete-user-fields mt-2" href="javascript:void(0);"
            wire:click="removeGenericCommentsFields({{ $key }})">
            <span wire.ignore.>
                <i data-feather="trash-2"></i>
            </span>
        </x-anchor-tag>
    </div>
</div>

@script
<script type="module">
    $(document).ready(function (event) {
        $(document).on('keyup change', '.quantity, .rate', function () {
            const row = $(this).closest('.row');
            const quantity = Number(row.find('.quantity').val().trim());
            const rate = Number(row.find('.rate').val().trim());

            if (!isNaN(quantity) && !isNaN(rate)) {  // Validate both values are numbers
                const amount = quantity * rate;
                const toFixedAmount = amount.toFixed(2);
                const amountId = row.find('.amount').attr('id');
                @this.set('invoiceForm.generic_comments.' + amountId + '.amount', toFixedAmount);  // Update amount with 2 decimal places
            } else {
                row.find('.amount').val('');  // Clear amount if invalid values entered
            }
        });
    });
</script>
@endscript
