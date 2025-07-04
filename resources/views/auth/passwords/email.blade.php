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
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .reset-info h5 {
            color: #1976d2;
            margin-bottom: 1rem;
        }

        .reset-info p {
            color: #424242;
            margin-bottom: 0;
        }
    </style>
@stop

@php
    $passwordEmailUrl = route('password.email');
    $loginUrl = route('login');
@endphp

@section('auth_header')
    <div class="text-center mb-3">
        <div class="security-badge">
            <i class="fas fa-shield-alt mr-1"></i>
            Administration Area
        </div>
    </div>
    <h4 class="text-center mb-3">Reset Your Password</h4>
@stop

@section('auth_body')
    {{-- Information box --}}
    <div class="reset-info">
        <h5><i class="fas fa-info-circle mr-2"></i>Password Reset Instructions</h5>
        <p>Enter your email address below and we'll send you a secure link to reset your password. For security reasons, this link will expire in 60 minutes.</p>
    </div>

    {{-- Success message --}}
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ $passwordEmailUrl }}" method="post" id="password-reset-form">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-4">
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="Enter your email address"
                   autofocus
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

        {{-- Submit buttons --}}
        <div class="row">
            <div class="col-6">
                <a href="{{ $loginUrl }}" class="btn btn-secondary btn-block">
                    <span class="fas fa-arrow-left mr-2"></span>
                    Back to Login
                </a>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="fas fa-paper-plane mr-2"></span>
                    Send Reset Link
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
            Secure password recovery for administration systems
        </small>
    </p>

    {{-- Help information --}}
    <div class="mt-3 text-center">
        <small class="text-muted">
            If you don't receive an email within 5 minutes, please check your spam folder or contact your system administrator.
        </small>
    </div>
@stop

@section('adminlte_js')
<script>
$(document).ready(function() {
    console.log('Password reset form loaded');

    // Form validation
    $('#password-reset-form').on('submit', function(e) {
        const email = $('input[name="email"]').val();

        if (!email) {
            e.preventDefault();
            alert('Please enter your email address.');
            return false;
        }

        // Basic email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return false;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="fas fa-spinner fa-spin mr-2"></span>Sending...');

        console.log('Password reset form submitting for:', email);
    });

    // Focus on email field
    $('input[name="email"]').focus();
});
</script>
@stop
