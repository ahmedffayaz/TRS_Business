@section('breadcrumbs', Breadcrumbs::render('business_edit', $slug))
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit</h4>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-12">
                @include('livewire.backend.business.form')
            </div>
        </div>
    </div>
</div>
