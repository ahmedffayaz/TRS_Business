@extends('layouts.app')
@section('title')
    Companies
@endsection
@section('content')
    <div class="content-body">
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <livewire:company-component />
            </div>
        </div>
    </div>
@endsection
