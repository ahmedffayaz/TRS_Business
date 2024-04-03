<div>
    <div class="text-center mb-2">
        <h1 class="mb-1">Invoice Payment</h1>
    </div>
    <form wire:submit.prevent="createInvoicePayment({{ $invoiceId }})">
        <div class="row mb-1">
            <div class="col-md-6">
                <x-input-label for="amount" class="required" value="Amount" />
                <div class="input-group input-group-merge">
                    <input type="number" class="form-control"  wire:model="invoicePaymentForm.amount"
                        min="0" step="0.01" placeholder="0.00" />
                    <span class="input-group-text">{{ $currency }}</span>
                </div>
                @error('invoicePaymentForm.amount')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-6">
                <x-input-label for="payment-date" class="required" value="Payment Date" />
                <x-input type="text" name="billed_at" id="payment-date" placeholder="October 14, 2020"
                    :class="$errors->has('invoicePaymentForm.billed_at') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                    wire:model="invoicePaymentForm.billed_at" autocomplete="off" />
                @error('invoicePaymentForm.billed_at')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                <x-input-label for="bank" class="required" value="Bank" />
                <x-input type="text" id="bank" :class="$errors->has('invoicePaymentForm.bank') ? 'error' : ''"
                    wire:model="invoicePaymentForm.bank" autocomplete="off" placeholder="Type bank name" />
                @error('invoicePaymentForm.bank')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-6">
                <x-input-label for="conversion_rate" class="required" value="Conversion Rate" />
                <div class="input-group input-group-merge">
                    <input type="number" class="form-control" wire:model="invoicePaymentForm.conversion_rate"
                        min="0" step="0.01" placeholder="0.00" />
                </div>
                @error('invoicePaymentForm.conversion_rate')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-label for="bank_charges" class="required" value="Bank Charges" />
                <div class="input-group input-group-merge">
                    <input type="number" class="form-control" wire:model="invoicePaymentForm.bank_charges"
                        min="0" step="0.01" placeholder="0.00" />
                    <span class="input-group-text">{{ $currency }}</span>
                </div>
                @error('invoicePaymentForm.bank_charges')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-label for="notes" value="Notes" />
                <x-textarea id="notes" rows="4" wire:model="invoicePaymentForm.notes" :class="$errors->has('invoicePaymentForm.notes') ? 'error' : ''" />
                @error('invoicePaymentForm.notes')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-4">
                <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Save</span>
                    <x-button-loader />
                </x-button>
            </div>
            <div class="col-md-8">
                <x-input-checkbox type="checkbox" id="send-email" name="send_email" wire:model="invoicePaymentForm.send_email"
                    statusClass="form-check-inline float-end" :labelValue="__('Send email')" />
            </div>
        </div>
    </form>
</div>

@script
    <script type="module">
        $(document).ready(function () {
            // Initialize flatpickr
            Livewire.dispatch('flatpickr');
            Livewire.on('reinitialize-flatpickr', () => {
                $(document).ready(function () {
                    Livewire.dispatch('flatpickr');
                });
            });
        });
    </script>
@endscript
