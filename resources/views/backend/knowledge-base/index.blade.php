@extends('backend.layouts.app')
@section('title', 'Knowledge Base')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/pages/page-knowledge-base.css') }}" />
@endpush
@section('content')
    @can('add_knowledgeBase')
        @section('breadcrumbs-button')
            <x-anchor-tag class="btn btn-primary open-modal" href="javascript:void(0)" data-act="ajax-modal"
                :value="__('Add Knowledge Base')" data-action-url="{{ route('dashboard.knowledge-bases.create') }}"
                data-method="get" data-complete-location="true" tabindex="0" aria-controls="table-hover" />
        @endsection
    @endcan
    {{-- Search knowledge base component --}}
    <x-search :title="__('Search knowledge base')">
        <form class="kb-search-input" action="{{ route('dashboard.knowledge-base.search') }}"
            method="GET" data-form-search=ajax-form autocomplete="off">
            @csrf
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i data-feather="search"></i></span>
                <input type="text" class="form-control" id="searchbar" name="search" autocomplete="off"
                    placeholder="Search knowledge base..." />
            </div>
        </form>
    </x-search>

    <section id="knowledge-base-content">
        <input type="hidden" name="page" id="page" value="0" />
        <div id="data"></div>
    </section>

    <x-main-modal wireIgnoreSelf="wire:ignore.self">
    </x-main-modal>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            fetchRecord("{{ route('dashboard.knowledge-base.fetch-record') }}")
        });
    </script>
@endpush
