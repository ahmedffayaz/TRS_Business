<div class="">
    <h1 class="mb-1">{{ $knowledgeBaseDetail->question }}</h1>
    <hr>
    @if (count($knowledgeBaseDetail->category->roles) > 0)
        <div class="my-2"><b>Roles:</b>
            @foreach ($knowledgeBaseDetail->category->roles as $role)
                <span class="badge {{ getRandomColor() }}">{{ $role->title }}</span>
            @endforeach
        </div>
    @endif
    <input type="hidden" value="{!! $knowledgeBaseDetail->answer !!}" id="knowledgeBase-modal-answer">
    <div class="my-2 knowledgeBase-modal-answer"></div>
    @if (count($knowledgeBaseDetail->keywords) > 0)
        <div class="my-2"><b>Keywords:</b> {{ implode(', ', $knowledgeBaseDetail->keywords->pluck('name')->toArray()) }}</div>
    @endif
</div>

@script
    <script type="module">
            $(document).ready(function () {
                Livewire.on('resetMte', () => {
                    $(document).ready(function () {
                        knowledgeBaseDetail();
                    });
                });

                function knowledgeBaseDetail() {
                    var answer = $('#knowledgeBase-modal-answer').val();
                    var converter = new Showdown.Converter();
                    $('.knowledgeBase-modal-answer').html(converter.makeHtml(answer));
                }

                knowledgeBaseDetail();
            });
    </script>
@endscript
