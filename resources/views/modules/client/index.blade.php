@extends('layouts.app')
@section('title')
    Clients
@endsection
@section('content')
    <div class="content-body">
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <livewire:client-component />
            </div>
        </div>
    </div>
@endsection
