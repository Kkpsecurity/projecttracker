@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* Custom 800px wide password reset box styling */
        .login-box {
            width: 800px !important;
            margin: 2% auto;
        }

        .card-body {
            padding: 2.5rem;
        }

        .login-logo {
            font-size: 2.1rem;
            font-weight: 300;
            margin-bottom: 2rem;
        }

        .login-logo a {
            color: #495057;
            text-decoration: none;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }

        /* Security-focused branding */
        .security-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .reset-info {
            background-color: #e8f5e8;
            border: 1px solid #c3e6c3;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .reset-info h5 {
            color: #155724;
            margin-bottom: 1rem;
        }

        .reset-info p {
            color: #424242;
            margin-bottom: 0;
        }

        .password-requirements {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .password-requirements h6 {
            color: #856404;
            margin-bottom: 1rem;
        }

        .password-requirements ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        .password-requirements li {
            color: #856404;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        /* Password strength indicator */
        .password-strength {
            height: 4px;
            background-color: #e9ecef;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .password-strength-fill {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
            width: 0%;
        }

        .strength-weak { background-color: #dc3545; width: 25%; }
        .strength-fair { background-color: #fd7e14; width: 50%; }
        .strength-good { background-color: #ffc107; width: 75%; }
        .strength-strong { background-color: #28a745; width: 100%; }
    </style>
@stop

@php
    $passwordResetUrl = route('password.update');
    $loginUrl = route('login');
@endphp

@section('auth_header')
    <div class="text-center mb-3">
        <div class="security-badge">
            <i class="fas fa-shield-alt mr-1"></i>
            Administration Area
        </div>
    </div>
    <h4 class="text-center mb-3">Set New Password</h4>
@stop

@section('auth_body')
    {{-- Success message --}}
    <div class="reset-info">
        <h5><i class="fas fa-check-circle mr-2"></i>Password Reset Verification</h5>
        <p>Your email has been verified. Please enter your new password below.</p>
    </div>

    {{-- Password requirements --}}
    <div class="password-requirements">
        <h6><i class="fas fa-lock mr-2"></i>Password Requirements</h6>
        <ul>
            <li>At least 8 characters long</li>
            <li>Contains at least one uppercase letter</li>
            <li>Contains at least one lowercase letter</li>
            <li>Contains at least one number</li>
            <li>Contains at least one special character (!@#$%^&*)</li>
        </ul>
    </div>

    <form action="{{ $passwordResetUrl }}" method="post" id="password-reset-form">
        @csrf

        {{-- Hidden token field --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email field (readonly) --}}
        <div class="input-group mb-3">
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ $email ?? old('email') }}"
                   placeholder="Email Address"
                   readonly
                   required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- New Password field --}}
        <div class="input-group mb-3">
            <input type="password"
                   name="password"
                   id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="New Password"
                   required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password strength indicator --}}
        <div class="password-strength">
            <div class="password-strength-fill" id="password-strength-fill"></div>
        </div>
        <small class="text-muted" id="password-strength-text">Enter a password to see strength</small>

        {{-- Confirm Password field --}}
        <div class="input-group mb-4">
            <input type="password"
                   name="password_confirmation"
                   id="password_confirmation"
                   class="form-control"
                   placeholder="Confirm New Password"
                   required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        {{-- Submit buttons --}}
        <div class="row">
            <div class="col-6">
                <a href="{{ $loginUrl }}" class="btn btn-secondary btn-block">
                    <span class="fas fa-arrow-left mr-2"></span>
                    Back to Login
                </a>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-primary btn-block" id="reset-btn">
                    <span class="fas fa-save mr-2"></span>
                    Reset Password
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    {{-- Security notice --}}
    <p class="mb-0 text-center">
        <small class="text-muted">
            <i class="fas fa-lock mr-1"></i>
            Secure password reset for administration systems
        </small>
    </p>
@stop

@section('adminlte_js')
<script>
$(document).ready(function() {
    console.log('Password reset form loaded');

    // Password strength checking
    $('#password').on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        updatePasswordStrength(strength);
    });

    // Form validation
    $('#password-reset-form').on('submit', function(e) {
        const password = $('#password').val();
        const passwordConfirm = $('#password_confirmation').val();

        if (!password || !passwordConfirm) {
            e.preventDefault();
            alert('Please fill in both password fields.');
            return false;
        }

        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Passwords do not match.');
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            return false;
        }

        // Show loading state
        const submitBtn = $('#reset-btn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="fas fa-spinner fa-spin mr-2"></span>Resetting...');

        console.log('Password reset form submitting...');
    });

    // Password confirmation matching
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const passwordConfirm = $(this).val();

        if (passwordConfirm && password !== passwordConfirm) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Focus on password field
    $('#password').focus();

    function checkPasswordStrength(password) {
        let score = 0;

        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        return score;
    }

    function updatePasswordStrength(score) {
        const fill = $('#password-strength-fill');
        const text = $('#password-strength-text');

        fill.removeClass('strength-weak strength-fair strength-good strength-strong');

        switch(score) {
            case 0:
            case 1:
                fill.addClass('strength-weak');
                text.text('Weak password').css('color', '#dc3545');
                break;
            case 2:
                fill.addClass('strength-fair');
                text.text('Fair password').css('color', '#fd7e14');
                break;
            case 3:
            case 4:
                fill.addClass('strength-good');
                text.text('Good password').css('color', '#ffc107');
                break;
            case 5:
                fill.addClass('strength-strong');
                text.text('Strong password').css('color', '#28a745');
                break;
        }
    }
});
</script>
@stop
