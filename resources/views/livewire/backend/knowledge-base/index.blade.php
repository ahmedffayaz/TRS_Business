<x-app-layout>
{{-- <div> --}}
    @can('add_knowledgeBase')
        @section('breadcrumbs-button')
            <x-anchor-tag class="btn btn-primary" href="javascript:void(0)" :value="__('Add Knowledge Base')" tabindex="0"
                aria-controls="table-hover" type="button" wire:click="openMainModal" wire:ignore />
        @endsection
    @endcan
    <section id="knowledge-base-search">
        <div class="row">
            <div class="col-12">
                <div class="card knowledge-base-bg text-center"
                    style="background-image: url({{ asset('assets/images/banner/banner.png') }})">
                    <div class="card-body">
                        <h2 class="text-primary">Dedicated Source Used on Website</h2>
                        <p class="card-text mb-2">
                            <span>Popular searches: </span><span class="fw-bolder">Sales automation, Email
                                marketing</span>
                        </p>
                        <form class="kb-search-input">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i data-feather="search"></i></span>
                                <input type="text" class="form-control" id="searchbar"
                                    placeholder="Search knowledge base..." />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="knowledge-base-content">
        <div class="row kb-search-content-info match-height">
            @forelse ($knowledgeBases as $knowledgeBase)
                <!-- knowledge base card -->
                <div class="col-md-4 col-sm-6 col-12 kb-search-content">
                    <div class="card">
                        <a href="">
                            <img src="{{ !file_exists(public_path('storage/images/knowledge-base/' . $knowledgeBase->image)) || is_null($knowledgeBase->image) ? asset('assets/illustrations/questions.svg') : asset('storage/images/knowledge-base/' . $knowledgeBase->image) }}"
                                class="card-img-top" alt="knowledge-base-image" />
                            <div class="card-body text-center">
                                <h4>{{ substr($knowledgeBase->name, 0, 40) }}</h4>
                                <p class="text-body">{{ substr($knowledgeBase->description, 0, 105) }} </p>
                                <div class="mt-1">
                                    @can('edit_knowledgeBase')
                                        <a href="javascript:void(0);" class="btn btn-icon btn-primary"
                                            wire:click="edit('{{ $knowledgeBase?->id }}')">
                                            <span wire:ignore><i data-feather="edit-2"></i></span>
                                        </a>
                                    @endcan
                                    @can('delete_knowledgeBase')
                                        <a href="javascript:void(0);" class="btn btn-icon btn-danger"
                                            wire:click="deleteConfirmation('{{ $knowledgeBase?->id }}')">
                                            <span wire:ignore><i data-feather="trash"></i></span>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @empty
                <!-- no result -->
                <div class="col-12 text-center no-result no-items">
                    <h4 class="mt-4">Search result not found!!</h4>
                </div>
            @endforelse
            {{ $knowledgeBases->links('components.pagination') }}
        </div>
    </section>
{{-- </div> --}}
</x-app-layout>
