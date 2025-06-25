@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-lg-12 d-flex justify-content-between align-items-center">
                <h2>Owners List</h2>
                <div>
                    <a class="btn btn-primary" href="{{ route('admin.dashboard') }}">Back</a>
                    <a class="btn btn-success" href="{{ route('admin.owners.create') }}">Add New Owner</a>
                    <a class="btn btn-warning" href="{{ route('admin.owners.export') }}">Export</a>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        @include('admin.owners.tables.owners')
    </div>
@endsection
