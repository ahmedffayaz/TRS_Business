<div class="">
    <h1 class="mb-1">{{ $knowledgeBaseDetail->question }}</h1>
    <hr>
    <div class="my-2">Roles:
        @foreach ($knowledgeBaseDetail->category->roles as $role)
            <span class="badge rounded-pill {{ getRandomColor() }}">{{ $role->name }}</span>
        @endforeach
    </div>
    <div class="my-2">
        {!! $knowledgeBaseDetail->answer !!}
    </div>
    <div class="my-2">
        @foreach ($knowledgeBaseDetail->keywords as $keyword)
            <div class="badge rounded-pill {{ getRandomColor() }}">{{ $keyword->name }}</div>
        @endforeach
    </div>
</div>
