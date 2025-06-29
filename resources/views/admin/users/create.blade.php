@extends('adminlte::page')

@section('title', 'Create User - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Create New User</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Admin Center</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                <li class="breadcrumb-item active">Create User</li>
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
                    <i class="fas fa-user-plus mr-2"></i>
                    Create New User
                </h3>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="required">Full Name</label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
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
                                       value="{{ old('email') }}"
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="required">Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required
                                           placeholder="Enter password">
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
                                <label for="password_confirmation" class="required">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password"
                                           name="password_confirmation"
                                           id="password_confirmation"
                                           class="form-control"
                                           required
                                           placeholder="Confirm password">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="email_verified"
                                           id="email_verified"
                                           class="form-check-input"
                                           value="1"
                                           {{ old('email_verified') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_verified">
                                        Email Verified
                                        <small class="text-muted d-block">Skip email verification for this user</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="is_active"
                                           id="is_active"
                                           class="form-check-input"
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Account
                                        <small class="text-muted d-block">User can log in to the system</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="send_welcome_email"
                                           id="send_welcome_email"
                                           class="form-check-input"
                                           value="1"
                                           {{ old('send_welcome_email', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_welcome_email">
                                        Send Welcome Email
                                        <small class="text-muted d-block">Send account details to user's email</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Note -->
                    <div class="form-group">
                        <label for="admin_notes">Admin Notes (Optional)</label>
                        <textarea name="admin_notes"
                                  id="admin_notes"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Add any internal notes about this user...">{{ old('admin_notes') }}</textarea>
                        <small class="form-text text-muted">These notes are only visible to administrators.</small>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                Create User
                            </button>
                            <button type="button" class="btn btn-info ml-2" onclick="generatePassword()">
                                <i class="fas fa-key mr-1"></i>
                                Generate Password
                            </button>
                        </div>
                        <div class="col-md-6 text-right">
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
    }, 3000);

    alert('Password generated! It will be hidden in 3 seconds for security.');
}

// Real-time password validation
$(document).ready(function() {
    $('#password').on('input', function() {
        const password = $(this).val();
        let score = 0;
        let feedback = [];

        if (password.length >= 8) score++;
        else feedback.push('At least 8 characters');

        if (/[a-z]/.test(password)) score++;
        else feedback.push('Lowercase letter');

        if (/[A-Z]/.test(password)) score++;
        else feedback.push('Uppercase letter');

        if (/[0-9]/.test(password)) score++;
        else feedback.push('Number');

        if (/[^A-Za-z0-9]/.test(password)) score++;
        else feedback.push('Special character');

        // Update UI based on score
        const $field = $(this);
        $field.removeClass('is-valid is-invalid');

        if (score >= 4) {
            $field.addClass('is-valid');
        } else if (password.length > 0) {
            $field.addClass('is-invalid');
        }
    });

    // Password confirmation matching
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();

        $(this).removeClass('is-valid is-invalid');

        if (confirmation.length > 0) {
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
