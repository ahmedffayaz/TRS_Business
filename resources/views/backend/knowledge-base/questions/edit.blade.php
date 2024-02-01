@php
    $isEdit = isset($question) ? true : false;
    $url = $isEdit ? route('dashboard.knowledge-base-question.update', $question->id) : route('dashboard.knowledge-base-question.store');
@endphp
<div class="text-center mb-2">
    <h1 class="mb-1">{{ $isEdit ? 'Edit' : 'Add' }} Knowledge Base Question</h1>
</div>
<form class="row" action="{{ $url }}" method="POST" data-form=ajax-form>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <x-input type="hidden" name="knowledge_base_topic_id" value="{{ $knowledgeBaseTopicId }}" />
    <div class="col-md-12 mb-1">
        <x-input-label class="required" for="question" value="Question" />
        <x-input type="text" class="form-control" name="question" value="{{ $isEdit ? $question->question : '' }}"
            placeholder="Enter knowledge base question" data-msg="Please enter knowledge base question" />
    </div>

    <div class="col-md-12 mb-1">
        <x-input-label class="required" for="keywords" value="Keywords" />
        <x-input type="text" class="form-control" name="keywords" value="{{ $isEdit ? $question->keywords : '' }}"
            placeholder="Enter knowledge base keywords" data-msg="Please enter knowledge base keywords" />
    </div>

    <div class="col-md-12 mb-3">
        <x-input-label class="required" for="answer" value="Answer" />
        <x-input type="hidden" name="description" />
        <div class="editor">{!! $isEdit ? $question->answer : '' !!}</div>
    </div>

    <div class="col-12 text-center mt-5">
        <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light"
            type="submit" tabindex="4" data-button="submit"
        >{{ $isEdit ? 'Update' : 'Add' }}</x-button>
    </div>
</form>
