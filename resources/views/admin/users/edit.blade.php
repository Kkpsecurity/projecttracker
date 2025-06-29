@extends('adminlte::page')

@section('title', 'Edit User - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Edit User: {{ $user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Admin Center</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-edit mr-2"></i>
                    Edit User: {{ $user->name }}
                    @if($user->id == auth()->id())
                        <span class="badge badge-primary ml-2">Your Account</span>
                    @endif
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <!-- User Info Header -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->adminlte_image() }}"
                                     alt="{{ $user->name }}"
                                     class="img-circle elevation-2 mr-3"
                                     style="width: 60px; height: 60px;">
                                <div>
                                    <h5 class="mb-1">{{ $user->name }}</h5>
                                    <p class="text-muted mb-1">{{ $user->email }}</p>
                                    <small class="text-muted">
                                        Member since {{ $user->created_at->format('M d, Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <h5 class="mb-3">
                        <i class="fas fa-user mr-2"></i>
                        Basic Information
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="required">Full Name</label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       placeholder="Enter full name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="required">Email Address</label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       placeholder="Enter email address">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <h5 class="mt-4 mb-3">
                        <i class="fas fa-key mr-2"></i>
                        Password (Optional)
                    </h5>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Leave password fields empty to keep the current password unchanged.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Enter new password (optional)">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                            <i id="password-icon" class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Password must be at least 8 characters long and contain uppercase, lowercase, numbers, and special characters.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password_confirmation"
                                           id="password_confirmation"
                                           class="form-control"
                                           placeholder="Confirm new password">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                            <i id="password_confirmation-icon" class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <h5 class="mt-4 mb-3">
                        <i class="fas fa-cog mr-2"></i>
                        Account Settings
                    </h5>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="email_verified"
                                           id="email_verified"
                                           class="form-check-input"
                                           value="1"
                                           {{ old('email_verified', $user->email_verified_at) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_verified">
                                        Email Verified
                                        <small class="text-muted d-block">User's email is verified</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="is_active"
                                           id="is_active"
                                           class="form-check-input"
                                           value="1"
                                           {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                                           @if($user->id == auth()->id()) disabled @endif>
                                    <label class="form-check-label" for="is_active">
                                        Active Account
                                        <small class="text-muted d-block">User can log in to the system</small>
                                    </label>
                                    @if($user->id == auth()->id())
                                        <small class="text-warning">You cannot deactivate your own account</small>
                                        <input type="hidden" name="is_active" value="1">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="force_password_reset"
                                           id="force_password_reset"
                                           class="form-check-input"
                                           value="1"
                                           {{ old('force_password_reset') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="force_password_reset">
                                        Force Password Reset
                                        <small class="text-muted d-block">User must change password on next login</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="send_notification"
                                           id="send_notification"
                                           class="form-check-input"
                                           value="1"
                                           {{ old('send_notification', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_notification">
                                        Send Notification
                                        <small class="text-muted d-block">Email user about changes</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Statistics -->
                    <h5 class="mt-4 mb-3">
                        <i class="fas fa-chart-line mr-2"></i>
                        Account Statistics
                    </h5>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Login Count</span>
                                    <span class="info-box-number">{{ $user->login_count ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Last Login</span>
                                    <span class="info-box-number" style="font-size: 12px;">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">2FA Status</span>
                                    <span class="info-box-number" style="font-size: 12px;">
                                        {{ $user->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-user-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Sessions</span>
                                    <span class="info-box-number">{{ $user->active_sessions ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="form-group mt-4">
                        <label for="admin_notes">Admin Notes (Optional)</label>
                        <textarea name="admin_notes"
                                  id="admin_notes"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Add any internal notes about this user...">{{ old('admin_notes', $user->admin_notes ?? '') }}</textarea>
                        <small class="form-text text-muted">These notes are only visible to administrators.</small>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                Update User
                            </button>
                            <button type="button" class="btn btn-info ml-2" onclick="generatePassword()">
                                <i class="fas fa-key mr-1"></i>
                                Generate Password
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info mr-2">
                                <i class="fas fa-eye mr-1"></i>
                                View Profile
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.required:after {
    content: " *";
    color: red;
}

.card-primary.card-outline {
    border-top: 3px solid #007bff;
}

.form-check-label {
    font-weight: 500;
}

.form-check-label small {
    font-weight: normal;
}

.info-box {
    margin-bottom: 1rem;
}
</style>
@stop

@section('js')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function generatePassword() {
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";

    // Ensure at least one character from each required type
    password += "ABCDEFGHIJKLMNOPQRSTUVWXYZ"[Math.floor(Math.random() * 26)]; // Uppercase
    password += "abcdefghijklmnopqrstuvwxyz"[Math.floor(Math.random() * 26)]; // Lowercase
    password += "0123456789"[Math.floor(Math.random() * 10)]; // Number
    password += "!@#$%^&*"[Math.floor(Math.random() * 8)]; // Special

    // Fill the rest randomly
    for (let i = password.length; i < length; i++) {
        password += charset[Math.floor(Math.random() * charset.length)];
    }

    // Shuffle the password
    password = password.split('').sort(() => 0.5 - Math.random()).join('');

    document.getElementById('password').value = password;
    document.getElementById('password_confirmation').value = password;

    // Show password temporarily
    togglePassword('password');
    setTimeout(() => {
        if (document.getElementById('password').type === 'text') {
            togglePassword('password');
        }
    }, 5000);

    alert('Password generated! It will be hidden in 5 seconds for security.');
}

// Real-time password validation
$(document).ready(function() {
    $('#password').on('input', function() {
        const password = $(this).val();
        let score = 0;

        if (password.length === 0) {
            $(this).removeClass('is-valid is-invalid');
            return;
        }

        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        // Update UI based on score
        const $field = $(this);
        $field.removeClass('is-valid is-invalid');

        if (score >= 4) {
            $field.addClass('is-valid');
        } else {
            $field.addClass('is-invalid');
        }
    });

    // Password confirmation matching
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();

        $(this).removeClass('is-valid is-invalid');

        if (confirmation.length > 0 || password.length > 0) {
            if (password === confirmation) {
                $(this).addClass('is-valid');
            } else {
                $(this).addClass('is-invalid');
            }
        }
    });
});
</script>
@stop
