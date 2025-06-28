@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">KKP Security Project Tracker - Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>Welcome to KKP Security Project Tracker!</h4>
                    <p>You are successfully logged in to the project management system.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>System Status:</strong> ✅ Fresh Laravel installation with working authentication
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
