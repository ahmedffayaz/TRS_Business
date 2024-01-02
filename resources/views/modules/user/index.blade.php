@extends('layouts.app')
@section('title')
    Employees
@endsection
@section('content')
    <div class="content-body">
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <livewire:user-component />
            </div>
        </div>
    </div>
@endsection
