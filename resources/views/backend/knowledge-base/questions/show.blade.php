@extends('backend.layouts.app')
@section('title', $question->question)

@section('content')
    <!-- Knowledge base question Content  -->
    <section id="knowledge-base-question">
        <div class="row">
            <div class="col-lg-3 col-md-5 col-12 order-2 order-md-1">
                <div class="card">
                    <div class="card-body">
                        <h6 class="kb-title">
                            <i data-feather="info" class="font-medium-4 me-50"></i><span>Related Questions</span>
                        </h6>

                        <div class="list-group list-group-circle mt-1">
                            @forelse ($relatedQuestions as $relatedQuestion)
                                <x-anchor-tag
                                    href="{{ route('dashboard.knowledge-base-question.show', $relatedQuestion->id) }}"
                                    class="list-group-item text-body" :value="$relatedQuestion->question" />
                            @empty
                                <p>No question found</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-7 col-12 order-1 order-md-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-1">
                            <i data-feather="smartphone" class="font-medium-5 me-25"></i>
                            <span>{{ $question->question }}</span>
                        </h4>
                        <p class="mb-2">Last updated on {{ Carbon\Carbon::parse($question->updated_at)->format('d F, Y') }}</p>
                        {!! $question->answer !!}
                        <p></p>
                        @php $keywords = explode(', ', $question->keywords) @endphp
                        @foreach ($keywords as $keyword)
                            <div class="badge rounded-pill {{ getRandomColor() }}">
                                <x-anchor-tag href="javascript:void(0);" onclick="submitKeyword('{{ $keyword }}')" :value="$keyword" />
                            </div>
                        @endforeach
                        <form id="search-keyword" action="{{ route('dashboard.knowledge-base.search-keyword') }}" method="post" class="d-none">
                            @csrf
                            <x-input type="hidden" name="keyword" id="keyword-input" />
                        </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Knowledge base question Content ends -->
@endsection

@push('scripts')
    <script>
        function submitKeyword(keyword) {
            document.getElementById('keyword-input').value = keyword;
            document.getElementById('search-keyword').submit();
        }
    </script>
@endpush
