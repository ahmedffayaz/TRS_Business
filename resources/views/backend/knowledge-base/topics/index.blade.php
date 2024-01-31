@extends('backend.layouts.app')
@section('title', 'Knowledge Base')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/pages/page-knowledge-base.css') }}" />
@endpush
@section('content')
    @can('add_knowledgeBase')
        @section('breadcrumbs-button')
            <x-anchor-tag class="btn btn-primary open-modal" href="javascript:void(0)" data-act="ajax-modal"
                :value="__('Add Knowledge Base Topic')" data-post-id="{{ $id }}"
                data-action-url="{{ route('dashboard.knowledge-base-topics.create') }}"
                data-method="get" data-complete-location="true" tabindex="0" aria-controls="table-hover" />
        @endsection
    @endcan
    <!-- Knowledge base Jumbotron -->
    <section id="kb-category-search">
        <div class="row">
            <div class="col-12">
                <div class="card knowledge-base-bg text-center" style="background-image: url('../../../app-assets/images/banner/banner.png')">
                    <div class="card-body">
                        <h2 class="text-primary">Dedicated Source Used on Website</h2>
                        <p class="card-text mb-2">
                            <span>Popular searches: </span><span class="fw-bolder">Sales automation, Email marketing</span>
                        </p>
                        <form class="kb-search-input">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i data-feather="search"></i></span>
                                <input type="text" class="form-control" id="searchbar" placeholder="Ask a question..." />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Knowledge base Jumbotron -->

    <!-- Knowledge base category Content  -->
    <section id="knowledge-base-category">
        <input type="hidden" name="page" id="page" value="0" />
        <div id="data"></div>
    </section>
    <!--/ Knowledge base category Content -->

    <x-main-modal wireIgnoreSelf="wire:ignore.self">
    </x-main-modal>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let knowledgeBaseId = "{{ $id }}";
            let topicUrl = "{{ route('dashboard.knowledge-base-topic.fetch-record', ':id') }}";
            topicUrl = topicUrl.replace(':id', knowledgeBaseId);
            fetchRecord(topicUrl);
        });
    </script>
@endpush
