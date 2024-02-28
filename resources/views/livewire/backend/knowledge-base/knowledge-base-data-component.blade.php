<div>
    @section('breadcrumbs-button')
        <div class="d-flex">
            <div class="col-md-7">
                <x-anchor-tag class="btn btn-primary" href="javascript:void(0);"
                    :value="__('Add Knowledge Base')" tabindex="0" aria-controls="table-hover"
                    type="button" wire:click="openCreateKnowledgeBaseModal" wire:ignore />
            </div>
            <div class="col-md-5">
                <x-anchor-tag class="btn btn-primary" href="javascript:void(0);" :value="__('Add Category')"
                    tabindex="0" aria-controls="table-hover" type="button" wire:click="openKnowledgeBaseCategoryModal" wire:ignore />
            </div>
        </div>
    @endsection

    <div class="row mb-2">
        <div class="col-md-4 col-sm-12">
            <div class="input-group input-group-merge">
                <span class="input-group-text" id="basic-addon-search2" wire:ignore><i data-feather="search"></i></span>
                <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search..."
                    aria-label="Search..." aria-describedby="basic-addon-search2" />
            </div>
        </div>
    </div>
    <div class="row kb-search-content-info match-height">
        @forelse ($knowledgeBaseCategories as $category)
            <div class="col-md-4 col-sm-6 col-12 kb-search-content">
                <!-- account setting card -->
                <div class="card">
                    <div class="card-header">
                        <span wire:ignore.>
                            <i data-feather="settings" class="font-medium-4 me-50 text-primary"></i>
                        </span>
                        <h4 class="card-title">{{ strlen($category->name) > 30
                            ? substr($category->name, 0, 30) . '...'
                            : $category->name }}</h4>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                data-bs-toggle="dropdown">
                                <span wire:ignore.>
                                    <i data-feather="more-vertical"></i>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);"
                                    wire:click="edit({{ $category->id }})">
                                    <span wire:ignore.>
                                        <i data-feather="edit-2" class="me-50"></i>
                                    </span>
                                    <span>Edit</span>
                                </x-anchor-tag>
                                <x-anchor-tag class="dropdown-item delete" href="javascript:void(0)"
                                    wire:click="deleteCategoryConfirmation({{ $category->id }})">
                                    <span wire:ignore.>
                                        <i data-feather="trash" class="me-50"></i>
                                    </span>
                                    <span>Delete</span>
                                </x-anchor-tag>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-circle mt-2">
                            @forelse ($category->questions as $question)
                                <div class="row">
                                    <div class="col-md-10">
                                        <x-anchor-tag href="javascript:void(0)" wire:click="showKnowledgeBase({{ $question->id . ', ' . $category->id }})">
                                            <span class="list-group-item text-body">{{ $question->question }}</span>
                                        </x-anchor-tag>
                                        @if (count($question->keywords) > 0)
                                            <span><b>Keywords:</b>
                                                @foreach ($question->keywords as $keyword)
                                                    <x-anchor-tag href="{{ route('dashboard.knowledgebase.search-keyword', $keyword->name) }}">
                                                        {{ $keyword->name }}
                                                    </x-anchor-tag>
                                                    @if (!$loop->last),&nbsp;@endif
                                                @endforeach
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                            data-bs-toggle="dropdown">
                                                <span wire:ignore.>
                                                    <i data-feather="more-vertical"></i>
                                                </span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);"
                                                wire:click="editKnowledgeBase({{ $question->id }}, {{ $category->id }})">
                                                    <span wire:ignore.>
                                                        <i data-feather="edit-2" class="me-50"></i>
                                                    </span>
                                                    <span>Edit</span>
                                                </x-anchor-tag>
                                                <x-anchor-tag class="dropdown-item delete" href="javascript:void(0);"
                                                wire:click="deleteKnowledgeBaseConfirmation({{ $question->id }})">
                                                    <span wire:ignore.>
                                                        <i data-feather="trash" class="me-50"></i>
                                                    </span>
                                                    <span>Delete</span>
                                                </x-anchor-tag>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <p>No record found!</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- no result -->
            <div class="col-md-12 text-center kb-search-content">
                <h4 class="mt-4">Search result not found!!</h4>
            </div>
        @endforelse
        {{ $knowledgeBaseCategories->links('components.blade-pagination') }}
    </div>

    @if($isQuestionModalOpen)
        <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeQuestionModal">
            @include('livewire.backend.knowledge-base.question-form')
        </x-main-modal>
    @elseif($isShowKnowledgeBase)
        <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeKnowledgeBaseModal">
            @include('livewire.backend.knowledge-base.knowledge-base-detail')
        </x-main-modal>
    @else
        <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeCategoryModal">
            @include('livewire.backend.knowledge-base.category-form')
        </x-main-modal>
    @endif
</div>

@script
    <script type="module">
        $(document).ready(function () {
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            })
        })
    </script>
@endscript
