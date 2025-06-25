@extends('layouts.app')

@section('content')
    <style>
        body, .container {
            font-family: 'Arial', sans-serif;
            font-size: 1rem;
            line-height: 1.6;
            color: #343a40; /* Dark gray text for readability */
        }
        h2 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        .card {
            background-color: #ffffff; /* White background */
            color: #343a40; /* Dark text */
            font-size: 1rem;
            padding: 2rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            font-size: 1rem;
        }
        .alert {
            font-size: 1rem;
        }
    </style>

    <div class="container p-3">
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 style="color: #dee2e6">Add New Consultant</h2>
                    <a class="btn btn-primary" href="{{ route('admin.consultants.index') }}">Back</a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-light m-2 p-5 shadow">
            <form action="{{ route('admin.consultants.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.consultants.forms.form', ['consultant' => new App\Models\Consultant()])
            </form>
        </div>
    </div>
@endsection
