<div class="text-center mb-2">
    <h1 class="mb-1">{{ $categoryForm->isUpdate ? 'Edit' : 'Add' }} Knowledge Base Category</h1>
</div>
<form wire:submit.prevent="{{ $categoryForm->isUpdate ? 'update(' . $categoryForm->id . ')' : 'store' }}">
    <div class="row mb-2">
        <div class="col-md-12">
            <x-input-label for="name" class="required" value="Name" />
            <x-input type="text" name="name" id="name"
                :class="$errors->has('categoryForm.name') ? 'error' : ''"
                placeholder="Enter name" wire:model="categoryForm.name" />
            @error('categoryForm.name')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-md-12">
            <span wire:ignore.>
                <x-input-label for="roles" value="Roles" />
                <x-select-input name="roles" id="category-role-select" multiple
                    :class="$errors->has('categoryForm.roles') ? 'error select2' : 'select2'"
                    wire:model="categoryForm.roles">
                    @isset($roles)
                        @foreach ($roles as $role)
                            <option value="{{ $role?->id }}">{{ $role?->title }}</option>
                        @endforeach
                    @endisset
                </x-select-input>
            </span>
            @error('categoryForm.roles')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $categoryForm->isUpdate ? 'Update' : 'Add' }}</span>
                <span wire:loading>
                    <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                </span>
            </x-button>
        </div>
    </div>
</form>

@script
    <script>
        $(document).ready(function () {
            Livewire.dispatch('select-container');
            Livewire.on('resetSelectInput', () => {
                $(document).ready(function () {
                    Livewire.dispatch('select-container');
                })
            });
            Livewire.on('roles-select', (data) => {
                var $select = $('#category-role-select');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formRoles).trigger('change');
            });

            $('#category-role-select').on('change', function(e) {
                @this.set('categoryForm.roles', $(this).val());
            });
        })
    </script>
@endscript
