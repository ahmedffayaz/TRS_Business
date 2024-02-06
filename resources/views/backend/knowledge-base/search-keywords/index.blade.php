@extends('backend.layouts.app')
@section('title', 'Knowledge Base Search')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/pages/page-knowledge-base.css') }}" />
@endpush
@section('content')
    <!-- Knowledge base category Content  -->
    <section id="knowledge-base-category">
        <input type="hidden" name="page" id="page" value="0" />
        <div id="data"></div>
    </section>
    <!--/ Knowledge base category Content -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let keyword = "{{ $keyword }}";
            let topicUrl = "{{ route('dashboard.knowledge-base.fetch-search-keyword', ':keyword') }}";
            topicUrl = topicUrl.replace(':keyword', keyword);
            fetchRecord(topicUrl);
        });
    </script>
@endpush
