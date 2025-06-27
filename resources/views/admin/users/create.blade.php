@extends('layouts.admin')

@section('title', 'Create New User')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Create New User</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus"></i>
                        User Information
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Validation Errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name"
                                           class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter full name"
                                           value="{{ old('name') }}"
                                           required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           name="email" 
                                           id="email"
                                           class="form-control @error('email') is-invalid @enderror" 
                                           placeholder="Enter email address"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           name="password" 
                                           id="password"
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Enter password"
                                           required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 characters required.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           name="password_confirmation" 
                                           id="password_confirmation"
                                           class="form-control" 
                                           placeholder="Confirm password"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Information
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>User Roles:</strong></p>
                    <ul class="list-unstyled">
                        <li><span class="badge badge-danger">Super Admin</span> - Full system access</li>
                        <li><span class="badge badge-warning">Admin</span> - Administrative access</li>
                        <li><span class="badge badge-info">User</span> - Standard user access</li>
                    </ul>
                    
                    <p class="mt-3"><strong>Password Requirements:</strong></p>
                    <ul class="text-sm">
                        <li>Minimum 8 characters</li>
                        <li>Must be confirmed</li>
                        <li>User will be required to verify email</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
               </div>
           </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Password:</strong>
                     <input type="password" class="form-control" name="password" id="password" placeholder="Password" />
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12">
                 <div class="form-group">
                     <strong>Confirm Password:</strong>
                     <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" />
                 </div>
             </div>
             <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                 <button type="submit" class="btn btn-primary">Submit</button>
             </div>
         </div>

     </form>
 </div>
@endsection
