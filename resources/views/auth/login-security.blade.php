{{-- Security-focused AdminLTE Login Page --}}
@extends('adminlte::auth.auth-page')

@section('auth_header', 'KKP Security Access Portal')

@section('auth_body')
<div class="login-logo mb-4">
    <div class="d-flex flex-column align-items-center">
        {{-- Security Shield Icon --}}
        <div class="mb-3">
            <i class="fas fa-shield-alt fa-4x text-primary" style="color: #1f2937 !important;"></i>
        </div>
        {{-- Brand Text --}}
        <div class="text-center">
            <h3 class="mb-1 font-weight-bold" style="color: #1f2937; font-size: 1.5rem;">
                <strong>KKP SECURITY</strong>
            </h3>
            <p class="text-muted mb-0" style="font-size: 0.9rem; letter-spacing: 1px;">
                PROJECT TRACKER
            </p>
            <hr class="my-2" style="width: 80px; border-color: #dc3545;">
            <small class="text-muted" style="font-size: 0.8rem;">
                AUTHORIZED ACCESS ONLY
            </small>
        </div>
    </div>
</div>

{{-- Security Notice --}}
<div class="alert alert-warning border-left-warning shadow-sm mb-4" style="border-left: 4px solid #f39c12;">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
        <small class="mb-0">
            <strong>SECURITY NOTICE:</strong> This system is for authorized personnel only.
            All activities are monitored and logged.
        </small>
    </div>
</div>

{{-- Login Form --}}
<form action="{{ route('login') }}" method="post" class="needs-validation" novalidate>
    @csrf

    {{-- Email Input --}}
    <div class="form-group mb-3">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text bg-light border-right-0">
                    <i class="fas fa-user text-muted"></i>
                </div>
            </div>
            <input type="email"
                   name="email"
                   class="form-control border-left-0 @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="Security Clearance Email"
                   required
                   autocomplete="email"
                   autofocus>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- Password Input --}}
    <div class="form-group mb-4">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text bg-light border-right-0">
                    <i class="fas fa-lock text-muted"></i>
                </div>
            </div>
            <input type="password"
                   name="password"
                   class="form-control border-left-0 @error('password') is-invalid @enderror"
                   placeholder="Security Passphrase"
                   required
                   autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- Remember Me & Login --}}
    <div class="row mb-4">
        <div class="col-7">
            <div class="form-check">
                <input type="checkbox"
                       name="remember"
                       id="remember"
                       class="form-check-input"
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label text-muted" for="remember">
                    <small>Keep me signed in</small>
                </label>
            </div>
        </div>
        <div class="col-5">
            <button type="submit" class="btn btn-dark btn-block shadow-sm">
                <i class="fas fa-sign-in-alt mr-2"></i>
                <strong>SECURE LOGIN</strong>
            </button>
        </div>
    </div>
</form>

{{-- Security Footer --}}
<div class="text-center mt-4">
    <small class="text-muted">
        <i class="fas fa-info-circle mr-1"></i>
        Contact your security administrator for access issues
    </small>
</div>

{{-- Custom Styles --}}
<style>
    .login-box {
        width: 460px !important;
        margin: 3vh auto !important;
    }

    .card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 10px;
        width: 100%;
    }

    .card-body {
        padding: 2.5rem 3rem;
    }

    .form-control {
        border-radius: 5px;
        height: 45px;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: #1f2937;
        box-shadow: 0 0 0 0.2rem rgba(31, 41, 55, 0.25);
    }

    .btn-dark {
        background-color: #1f2937;
        border-color: #1f2937;
        height: 45px;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }

    .btn-dark:hover {
        background-color: #374151;
        border-color: #374151;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .input-group-text {
        border-radius: 5px 0 0 5px;
        width: 45px;
        justify-content: center;
    }

    .border-left-warning {
        border-left-color: #f39c12 !important;
    }

    body.login-page {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    .login-box {
        margin-top: 3vh !important;
    }

    @media (max-width: 768px) {
        .login-box {
            width: 90% !important;
            margin: 2vh auto !important;
        }

        .card-body {
            padding: 1.5rem 2rem;
        }

        .login-logo h3 {
            font-size: 1.3rem !important;
        }
    }

    @media (min-width: 992px) {
        .login-box {
            width: 480px !important;
        }
    }
</style>
@endsection

@section('auth_footer')
<div class="text-center text-white mt-4">
    <small>
        &copy; {{ date('Y') }} KKP Security. All rights reserved.
        <br>
        System secured with advanced monitoring and encryption.
    </small>
</div>
@endsection
