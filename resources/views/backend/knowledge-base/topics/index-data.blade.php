<div class="row kb-search-content-info match-height">
    @forelse ($topics as $topic)
        <div class="col-md-4 col-sm-6 col-12 kb-search-content">
            <!-- account setting card -->
            <div class="card">
                <div class="card-header">
                    <i data-feather="settings" class="font-medium-4 me-50 text-primary"></i>
                    <h4 class="card-title">{{ strlen($topic->name) > 30 ? substr($topic->name, 0, 30) . '...' : $topic->name }}</h4>
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                            <i data-feather="more-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="javascript:void(0);" data-id="" >
                                <i data-feather="edit-2" class="me-50"></i>
                                <span>Add Question</span>
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" data-post-knowledge_base_id="{{ $topic->knowledgeBase->id }}"
                            data-action-url="{{ route('dashboard.knowledge-base-topics.edit', $topic->id) }}"
                            data-method="get" data-complete-location="true" data-act="ajax-modal">
                                <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                <span>Edit</span>
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);">
                                <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                <span>Delete</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-circle mt-2">
                        @forelse ($topic->qas as $qa)
                            <div class="row">
                                <div class="col-md-10">
                                    <a href="">
                                        <span class="list-group-item text-body">{{ $qa->question }}</span>
                                    </a>
                                </div>
                                <div class="col-md-2">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                            <i data-feather="more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i data-feather="edit-2" class="me-50"></i>
                                                <span>Edit</span>
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <i data-feather="trash" class="me-50"></i>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div>No record found!!</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @empty
        <!-- no result -->
        <div class="col-12 text-center no-result no-items">
            <h4 class="mt-4">Search result not found!!</h4>
        </div>
    @endforelse
    {{-- {{ $topics->links('components.pagination') }} --}}
</div>
