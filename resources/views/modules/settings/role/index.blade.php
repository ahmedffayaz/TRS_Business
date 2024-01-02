@extends('layouts.app')
@section('title')
    Roles
@endsection
@section('content')
    <div class="content-body">
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <livewire:settings.role-component />
            </div>
        </div>
    </div>
@endsection
