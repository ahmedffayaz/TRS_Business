@php
    $isEdit = isset($knowledgeBase) ? true : false;
    $url = $isEdit ? route('dashboard.knowledge-bases.update', $knowledgeBase->id) : route('dashboard.knowledge-bases.store');
@endphp
<div class="text-center mb-2">
    <h1 class="mb-1">{{ $isEdit ? 'Edit' : 'Add' }} Knowledge Base</h1>
</div>
<form class="row form" action="{{ $url }}" method="POST" enctype="multipart/form-data" data-form=ajax-form>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    @if (!$isEdit)
        <x-input type="hidden" name="created_by" value="{{ auth()->user()->id }}" />
    @endif
    <x-input type="hidden" name="updated_by" value="{{ auth()->user()->id }}" />
    <div class="d-flex mb-1">
        <div class="col-md-6 pe-1">
            <x-input-label class="required" for="companies" value="Companies" />
            <x-select-input id="company-select" name="companies[]"
            class="select2 multi-select form-control" multiple data-placeholder="Please select...">
                <option value="" disabled>--Select Company--</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}"
                        {{ $isEdit && in_array($company->id, $knowledgeBase->companies->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $company->name }}</option>
                @endforeach
            </x-select-input>
        </div>
        <div class="col-md-6 ps-1">
            <x-input-label class="required" for="roles" value="Roles" />
            <x-select-input id="role-select" name="roles[]"
            class="select2 multi-select form-select" multiple data-placeholder="Please select...">
                <option value="" disabled>--Select Role--</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}"
                        {{ $isEdit && in_array($role->id, $knowledgeBase->roles->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </x-select-input>
        </div>
    </div>

    <div class="col-md-6 mb-1">
        <x-input-label class="required" for="name" value="Knowledge Base Name" />
        <x-input type="text" class="form-control" name="name" value="{{ $isEdit ? $knowledgeBase->name : '' }}"
            placeholder="Enter knowledge base name" data-msg="Please enter knowledge base name" />
    </div>
    <div class="col-md-6 mb-1">
        <x-input-label class="form-label" for="image" value="Image" />
        <x-input type="file" class="form-control" name="image" accept="image/png, image/jpe, image/jpeg" />
    </div>

    <div class="col-md-12 mb-4">
        <x-input-label class="form-label" for="description" value="Knowledge Base Description" />
        <x-textarea class="description" rows="4" placeholder="Knowledge base short description"
            name="description" >{{ $isEdit ? $knowledgeBase->description : '' }}</x-textarea>
    </div>

    <div class="col-12 text-center">
        <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light"
            type="submit" tabindex="4" data-button="submit"
        >{{ $isEdit ? 'Update' : 'Add' }}</x-button>
    </div>
</form>
