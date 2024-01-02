@extends('layouts.app')
@section('title')
    Permissions
@endsection
@section('content')
    <div class="content-body">
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <livewire:settings.permission-component />
            </div>
        </div>
    </div>
@endsection
