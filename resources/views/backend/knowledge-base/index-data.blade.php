<div class="row kb-search-content-info match-height">
    @forelse ($knowledgeBases as $knowledgeBase)
        <!-- knowledge base card -->
        <div class="col-md-4 col-sm-6 col-12 kb-search-content">
            <div class="card">
                <a href="{{ route('dashboard.knowledge-bases.show', $knowledgeBase->id) }}">
                    <img src="{{ !file_exists(public_path('storage/images/knowledge-base/' . $knowledgeBase->image)) || is_null($knowledgeBase->image) ? asset('assets/illustrations/questions.svg') : asset('storage/images/knowledge-base/' . $knowledgeBase->image) }}"
                        class="card-img-top" alt="knowledge-base-image" />
                    <div class="card-body text-center">
                        <h4>{{ substr($knowledgeBase->name, 0, 40) }}</h4>
                        <p class="text-body">{{ substr($knowledgeBase->description, 0, 105) }} </p>
                        <div class="mt-1">
                            @can('edit_knowledgeBase')
                                <a href="javascript:void(0);" class="btn btn-icon btn-primary edit"
                                data-action-url="{{ route('dashboard.knowledge-bases.edit', $knowledgeBase->id) }}"
                                data-method="get" data-complete-location="true" data-act="ajax-modal">
                                    <i data-feather="edit-2"></i>
                                </a>
                            @endcan
                            @can('delete_knowledgeBase')
                                <a href="javascript:void(0);" class="btn btn-icon btn-danger delete"
                                    data-url="{{ route('dashboard.knowledge-bases.destroy', $knowledgeBase->id) }}">
                                    <i data-feather="trash"></i>
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
    {{ $knowledgeBases->links('components.blade-pagination') }}
</div>
