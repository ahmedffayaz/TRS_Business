@section('breadcrumbs', Breadcrumbs::render('clients_edit', $client))
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Edit Client</h4>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-12">
                @include('livewire.backend.client.form')
            </div>
        </div>
    </div>
</div>
