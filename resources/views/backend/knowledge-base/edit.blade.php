@php
    $isEdit = isset($knowledgeBase) ? true : false;
    $url = $isEdit ? route('dashboard.knowledge-bases.edit', $knowledgeBase->id) : route('dashboard.knowledge-bases.create');
@endphp
<div class="text-center mb-2">
    <h1 class="mb-1">{{ $isEdit ? 'Edit' : 'Add' }} Knowledge Base</h1>
</div>
<form class="row" action="{{ $url }}" method="POST">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <div class="d-flex mb-1">
        <div class="col-md-6 pe-1">
            <span wire:ignore.>
            <x-input-label class="required" for="companies" value="Companies" />
            <x-select-input id="company-select" name="companies[]"
            class="select2 multi-select form-control @error('form.companies') is-invalid @enderror"
            multiple data-placeholder="Please select...">
                <option value="" disabled>--Select Company--</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </x-select-input>
            </span>
            @error('form.companies')
                <x-input-error :message="$message" />
            @enderror
        </div>
        <div class="col-md-6 ps-1">
            <span wire:ignore.>
            <x-input-label class="required" for="roles" value="Roles" />
            <x-select-input id="role-select" name="roles[]"
            class="select2 multi-select form-select @error('form.roles') is-invalid @enderror"
            multiple data-placeholder="Please select...">
                <option value="" disabled>--Select Role--</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </x-select-input>
            </span>
            @error('form.roles')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

    <div class="col-md-6 mb-1">
        <x-input-label class="required" for="name" value="Knowledge Base Name" />
        <x-input type="text" class="form-control" name="name" value="{{ $isEdig ? $knowledgeBase->name : '' }}"
            placeholder="Enter knowledge base name" data-msg="Please enter knowledge base name" />
        @error('form.name')
            <x-input-error :message="$message" />
        @enderror
    </div>
    <div class="col-md-6 mb-1">
        <x-input-label class="form-label" for="image" value="Image" />
        <x-input type="file" class="form-control" name="image" accept="image/png, image/jpe, image/jpeg" />
        @error('image')
            <x-input-error :message="$message" />
        @enderror
    </div>

    <div class="col-md-12 mb-4">
        <x-input-label class="form-label" for="description" value="Knowledge Base Description" />
        <x-textarea :class="$errors->has('form.description') ? 'error char-textarea' : 'char-textarea'" data-length="100" length="100" rows="4"
            placeholder="Knowledge base short description" name="description" >{{ $isEdit ? $knowledgeBase->description }}</x-textarea>
        @error('form.description')
            <x-input-error :message="$message" />
        @enderror
    </div>

    <div class="col-12 text-center">
        <button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4">
            {{ $isEdit ? 'Update' : 'Add' }}
        </button>
    </div>
</form>
