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
                            <a href="#" class="list-group-item text-body">How Secure Is My Password?</a>
                            <a href="#" class="list-group-item text-body">Can I Change My Username?</a>
                            <a href="#" class="list-group-item text-body">Where Can I Upload My Avatar?</a>
                            <a href="#" class="list-group-item text-body">How Do I Change My Timezone?</a>
                            <a href="#" class="list-group-item text-body">How Do I Change My Password?</a>
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
                        <br>
                        @php $keywords = explode(',', $question->keywords) @endphp
                        <div class="mt-2">@foreach ($keywords as $keyword)
                            {{ $keyword }}
                        @endforeach</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Knowledge base question Content ends -->
@endsection
