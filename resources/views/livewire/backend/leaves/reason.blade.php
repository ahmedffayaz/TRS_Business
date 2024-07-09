<form wire:submit.prevent="{{ $cancelReason ? 'cancelLeave' : 'rejectLeave' }}">
    <div class="row mb-1">
        <div class="col-md-12">
            <x-input-label for="reason" value="Reason" />
            <x-textarea id="reason" data-length="200" length="200" rows="3"
                :class="$errors->has('reason') ? 'error char-textarea' : 'char-textarea'"
                placeholder="Enter Reason" wire:model="reason"/>
            @error('reason')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>
    <div class="text-center">
        <x-button class="btn-primary me-1" type="submit" tabindex="4"
            wire:loading.attr="disabled">
                <span wire:loading.remove>Submit</span>
            <x-button-loader />
        </x-button>
    </div>
</form>
