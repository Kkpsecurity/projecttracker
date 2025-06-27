@extends('layouts.admin')

@section('title', 'Edit User')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Edit User</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Edit: {{ $user->name }}</li>
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
                        <i class="fas fa-user-edit"></i>
                        Edit User: {{ $user->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
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
                                           value="{{ old('name', $user->name) }}"
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
                                           value="{{ old('email', $user->email) }}"
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
                                    <label for="password">New Password</label>
                                    <input type="password" 
                                           name="password" 
                                           id="password"
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Leave blank to keep current password">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Leave blank to keep current password. Minimum 8 characters if changing.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" 
                                           name="password_confirmation" 
                                           id="password_confirmation"
                                           class="form-control" 
                                           placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="email_verified" 
                                               name="email_verified"
                                               {{ $user->email_verified_at ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="email_verified">
                                            Email Verified
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Check to mark email as verified.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update User
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
                        User Information
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>Current Status:</strong></p>
                    @if($user->email_verified_at)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Pending Verification</span>
                    @endif
                    
                    <p class="mt-3"><strong>User Role:</strong></p>
                    @if($user->id == 1)
                        <span class="badge badge-danger">Super Admin</span>
                    @elseif($user->id == 2)
                        <span class="badge badge-warning">Admin</span>
                    @else
                        <span class="badge badge-info">User</span>
                    @endif
                    
                    <p class="mt-3"><strong>Account Details:</strong></p>
                    <ul class="list-unstyled text-sm">
                        <li><strong>Created:</strong> {{ $user->created_at->format('M d, Y') }}</li>
                        <li><strong>Last Updated:</strong> {{ $user->updated_at->diffForHumans() }}</li>
                        <li><strong>ID:</strong> #{{ $user->id }}</li>
                    </ul>
                </div>
            </div>

            @if($user->id != 1 && $user->id != auth()->id())
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            Danger Zone
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Permanently delete this user account. This action cannot be undone.</p>
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
                    <input type="checkbox" id="enablePassword" class="form-check-input">
                    <label for="enablePassword" class="form-check-label">Change Password</label>
                </div>
                <label for="password" class="form-label"><strong>Password:</strong></label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" disabled>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary px-5">Submit</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('enablePassword').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            passwordField.disabled = !this.checked;
            if (!this.checked) {
                passwordField.value = ''; // Clear password when disabled
            }
        });
    </script>
@endsection
