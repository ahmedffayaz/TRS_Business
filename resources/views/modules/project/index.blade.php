@extends('layouts.app')
@section('title')
    Projects
@endsection
@section('content')
    <div class="content-body">
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <livewire:project-component />
            </div>
        </div>
    </div>
@endsection
