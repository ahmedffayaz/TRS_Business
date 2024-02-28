<div class="">
    <h1 class="mb-1">{{ $knowledgeBaseDetail->question }}</h1>
    <hr>
    @if (count($knowledgeBaseDetail->keywords) > 0)
        <div class="my-2"><b>Roles:</b> {{ implode(', ', $knowledgeBaseDetail->category->roles->pluck('title')->toArray()) }}</div>
    @endif
    <div class="my-2">
        {!! $knowledgeBaseDetail->answer !!}
    </div>
    @if (count($knowledgeBaseDetail->keywords) > 0)
        <div class="my-2"><b>Keywords:</b> {{ implode(', ', $knowledgeBaseDetail->keywords->pluck('name')->toArray()) }}</div>
    @endif
</div>
