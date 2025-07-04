@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* Custom 800px wide login box styling */
        .login-box {
            width: 700px !important;
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
    </style>
@stop

@php
    $loginUrl = route('login');
    $passResetUrl = route('password.request');
@endphp

@section('auth_header')
    <div class="text-center mb-3">
        <div class="security-badge">
            <i class="fas fa-shield-alt mr-1"></i>
            Administration Area
        </div>
    </div>
    <h4 class="text-center mb-3">Sign in to start your session</h4>
@stop

@section('auth_body')
    <form action="{{ $loginUrl }}" method="post" id="login-form">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="Email Address"
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

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Password"
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

        {{-- Login row --}}
        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
            </div>

            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="fas fa-sign-in-alt mr-2"></span>
                    Sign In
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    @if(Route::has('password.request'))
        <p class="mb-1">
            <a href="{{ route('password.request') }}" class="text-primary">
                <i class="fas fa-key mr-1"></i>
                I forgot my password
            </a>
        </p>
    @endif

    {{-- Security notice --}}
    <p class="mb-0 text-center">
        <small class="text-muted">
            <i class="fas fa-lock mr-1"></i>
            Secure access to administration systems
        </small>
    </p>
@stop

@section('adminlte_js')
<script>
$(document).ready(function() {
    console.log('Administration area login form loaded');

    // Form validation
    $('#login-form').on('submit', function(e) {
        const email = $('input[name="email"]').val();
        const password = $('input[name="password"]').val();

        if (!email || !password) {
            e.preventDefault();
            alert('Please enter both email and password.');
            return false;
        }

        console.log('Login form submitting...');
    });

    // Focus management
    if (!$('input[name="email"]').val()) {
        $('input[name="email"]').focus();
    } else {
        $('input[name="password"]').focus();
    }
});
</script>
@stop
