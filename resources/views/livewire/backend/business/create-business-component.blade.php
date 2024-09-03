<div>
    @section('breadcrumbs', Breadcrumbs::render('business_create'))
    @if (Session::has('success'))
        <div class="alert alert-success p-1">
            {{ Session::get('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Add Business</h4>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-12">
                    @include('livewire.backend.business.form')
                </div>
            </div>
        </div>
    </div>
</div>
