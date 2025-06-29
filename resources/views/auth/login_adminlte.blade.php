@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php
    $loginUrl = route('admin.login');
    $passResetUrl = route('password.request', [], false);
@endphp

@section('auth_header', 'Sign in to start your session')

@section('auth_body')
    <form action="{{ $loginUrl }}" method="post" id="adminlte-login-form">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="Email"
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
                    <span class="fas fa-sign-in-alt"></span>
                    Sign In
                </button>
            </div>
        </div>
    </form>

    {{-- CSRF Debug Info --}}
    @if(config('app.debug'))
        <div class="mt-3 small text-muted">
            <div>Session ID: {{ session()->getId() }}</div>
            <div>CSRF Token: <span id="csrf-display">{{ csrf_token() }}</span></div>
        </div>
    @endif
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    @if(Route::has('password.request'))
        <p class="my-0">
            <a href="{{ route('password.request') }}">
                I forgot my password
            </a>
        </p>
    @endif
@stop

@section('adminlte_js')
<script>
$(document).ready(function() {
    console.log('AdminLTE Login form loaded');
    console.log('Initial CSRF token:', $('input[name="_token"]').val());
    console.log('Session domain:', '{{ config("session.domain") }}');

    $('#adminlte-login-form').on('submit', function(e) {
        console.log('AdminLTE form submitting - refreshing token...');

        // Get fresh token synchronously before form submission
        $.ajax({
            url: '{{ route("admin.csrf.token") }}',
            method: 'GET',
            async: false, // Synchronous to ensure we have fresh token
            success: function(data) {
                console.log('Fresh AdminLTE token received:', data.token);
                $('input[name="_token"]').val(data.token);
                $('#csrf-display').text(data.token);
            },
            error: function(xhr, status, error) {
                console.error('Failed to get fresh token:', error);
                console.log('Proceeding with existing token');
            }
        });

        console.log('Submitting AdminLTE form with token:', $('input[name="_token"]').val());
        // Let the form submit normally after token refresh
    });
});
</script>
@stop
