@if ($form->isUpdate)
    <form wire:submit.prevent="update({{ $form?->id }})">
        <div class="row">
            <div class="col-md-12">
                <x-input-label for="subject" value="Subject" />
                <x-input id="subject" :class="$errors->has('form.subject') ? 'error' : ''" wire:model="form.subject" />
                @error('invoiceForm.all_comments')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-12">
                <x-input-label for="allowed-tag" value="Allowed Tags" />
                <x-input id="allowed-tag" wire:model="form.keywords" disabled />
            </div>
            <div class="col-md-12">
                <x-input-label for="message" value="Message" />
                <x-textarea id="message" :class="$errors->has('form.body') ? 'error' : ''" wire:model="form.body" />
            </div>
        </div>
    </form>
@endif
