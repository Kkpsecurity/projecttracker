@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h2 class="text-white">Consultants List</h2>
                <div>
                    <a class="btn btn-primary" href="{{ route('admin.hb837.index') }}">Back</a>
                    <a class="btn btn-success" href="{{ route('admin.consultants.create') }}">Add New Consultant</a>
                    <a class="btn btn-warning" href="{{ route('admin.consultants.export') }}">Export</a>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success mt-3">
                <p>{{ $message }}</p>
            </div>
        @endif

        @include('admin.consultants.tables.consultants')
    </div>
@endsection
