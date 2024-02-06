@php
    $isEdit = isset($topic) ? true : false;
    $url = $isEdit ? route('dashboard.knowledge-base-topics.update', $topic->id) : route('dashboard.knowledge-base-topics.store');
@endphp
<div class="text-center mb-2">
    <h1 class="mb-1">{{ $isEdit ? 'Edit' : 'Add' }} Knowledge Base Topic</h1>
</div>
<form class="row" action="{{ $url }}" method="POST" data-form=ajax-form>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <x-input type="hidden" name="knowledge_base_id" value="{{ $knowledgeBaseId }}" />
    <div class="col-md-12 mb-1">
        <x-input-label class="required" for="name" value="Topic Name" />
        <x-input type="text" class="form-control" name="name" value="{{ $isEdit ? $topic->name : '' }}"
            placeholder="Enter topic name" data-msg="Please enter topic name" />
    </div>

    <div class="col-12 text-center">
        <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light"
            type="submit" tabindex="4" data-button="submit"
        >{{ $isEdit ? 'Update' : 'Add' }}</x-button>
    </div>
</form>
