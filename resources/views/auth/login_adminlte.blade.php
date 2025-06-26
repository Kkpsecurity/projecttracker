@extends('adminlte::auth.login')

@section('adminlte_css_pre')
    <style>
        .login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            font-weight: bold;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 30px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
@stop

@section('title', 'Project Tracker - Login')

@section('auth_header', 'Project Tracker')

@section('auth_body')
    <form method="POST" action="{{ route('admin.login') }}" id="login-form">
        @csrf
        
        <!-- Email Input -->
        <div class="input-group mb-3">
            <input type="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   placeholder="Email Address"
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="email" 
                   autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Password Input -->
        <div class="input-group mb-3">
            <input type="password" 
                   name="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   placeholder="Password"
                   required 
                   autocomplete="current-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </div>
        </div>
    </form>

    <!-- Forgot Password Link -->
    @if (Route::has('admin.password.request'))
        <p class="mb-1 text-center mt-3">
            <a href="{{ route('admin.password.request') }}" class="text-center">
                I forgot my password
            </a>
        </p>
    @endif
@stop

@section('adminlte_js')
    <script>
        $(document).ready(function() {
            // Add additional CSRF token handling
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Handle form submission with enhanced error handling
            $('#login-form').on('submit', function(e) {
                // Get fresh CSRF token if needed
                const token = $('input[name="_token"]').val();
                if (!token) {
                    e.preventDefault();
                    console.log('No CSRF token found, refreshing page...');
                    window.location.reload();
                    return false;
                }
                
                // Add loading state
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Logging in...');
                
                // Re-enable button after 10 seconds as fallback
                setTimeout(function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html('<i class="fas fa-sign-in-alt"></i> Login');
                }, 10000);
            });
            
            // Auto-focus on email field
            $('input[name="email"]').focus();
        });
    </script>
@stop
