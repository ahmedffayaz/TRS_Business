<div class="row kb-search-content-info match-height">
    @forelse ($topics as $topic)
        <div class="col-md-4 col-sm-6 col-12 kb-search-content">
            <!-- account setting card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="kb-title">
                        <i data-feather="settings" class="font-medium-4 me-50 text-primary"></i>
                        <span>{{ strlen($topic->name) > 30 ? substr($topic->name, 0, 30) . '...' : $topic->name }}</span>
                    </h4>
                    <div class="list-group list-group-circle mt-2">
                        @forelse ($topic->qas as $qa)
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('dashboard.knowledge-base-question.show', $qa->id) }}">
                                        <span class="list-group-item text-body">{{ $qa->question }}</span>
                                    </a>
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
        <div class="col-md-12 text-center kb-search-content">
            <h4 class="mt-4">Search result not found!!</h4>
        </div>
    @endforelse
    {{ $topics->links('components.blade-pagination') }}
</div>
