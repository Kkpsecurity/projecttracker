@extends('layouts.app')

@section('content')
<div class="container vh-100">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow" style="margin-top: 250px;">
                <div class="card-header">{{ __('Login') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.login') }}" id="loginForm">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Login page JavaScript loaded');
    console.log('Current CSRF token:', $('meta[name="csrf-token"]').attr('content'));
    console.log('Form CSRF token:', $('input[name="_token"]').val());
    console.log('Form action:', $('#loginForm').attr('action'));

    // Variable to prevent double submission
    var isSubmitting = false;

    // Refresh CSRF token before form submission
    $('#loginForm').on('submit', function(e) {
        console.log('Form submission intercepted');

        // Prevent double submission
        if (isSubmitting) {
            console.log('Already submitting, ignoring...');
            e.preventDefault();
            return false;
        }

        e.preventDefault();
        isSubmitting = true;

        // Show loading state
        var submitBtn = $('#loginForm button[type="submit"]');
        var originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Logging in...');

        // Get fresh CSRF token
        console.log('Requesting fresh CSRF token...');
        $.get('{{ route("admin.csrf.token") }}')
            .done(function(data) {
                console.log('Fresh CSRF token received:', data.token);

                // Update the hidden CSRF input field
                $('input[name="_token"]').val(data.token);
                // Update the meta tag for future AJAX requests
                $('meta[name="csrf-token"]').attr('content', data.token);

                console.log('Updated form token:', $('input[name="_token"]').val());
                console.log('Submitting form with fresh token...');

                // Create a new form element to avoid infinite loop
                var formData = $('#loginForm').serialize();
                var formAction = $('#loginForm').attr('action');

                console.log('Form data:', formData);
                console.log('Posting to:', formAction);

                // Submit via AJAX to get better error handling
                $.ajax({
                    url: formAction,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': data.token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .done(function(response, textStatus, xhr) {
                    console.log('Login successful, redirecting...');
                    // If successful, redirect to dashboard
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '{{ route("admin.home.index") }}';
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Login failed:', xhr.status, error);

                    if (xhr.status === 419) {
                        console.error('CSRF token mismatch!');
                        alert('Session expired. Please refresh the page and try again.');
                        location.reload();
                    } else if (xhr.status === 422) {
                        // Validation errors
                        console.log('Validation errors:', xhr.responseJSON);
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            var errorMessage = '';
                            for (var field in errors) {
                                errorMessage += errors[field].join('\n') + '\n';
                            }
                            alert('Login failed:\n' + errorMessage);
                        }
                    } else if (xhr.status === 302) {
                        // Redirect - might be successful
                        console.log('Received redirect, following...');
                        var redirectUrl = xhr.getResponseHeader('Location');
                        if (redirectUrl) {
                            window.location.href = redirectUrl;
                        } else {
                            window.location.href = '{{ route("admin.home.index") }}';
                        }
                    } else {
                        alert('Login failed: ' + (xhr.responseJSON?.message || error));
                    }

                    // Reset form state
                    isSubmitting = false;
                    submitBtn.prop('disabled', false).text(originalText);
                });
            })
            .fail(function(xhr, status, error) {
                console.error('Failed to get fresh CSRF token:', status, error);
                alert('Failed to refresh security token. Please refresh the page and try again.');

                // Reset form state
                isSubmitting = false;
                submitBtn.prop('disabled', false).text(originalText);
            });
    });

    // Auto-refresh CSRF token periodically (every 5 minutes for testing)
    setInterval(function() {
        console.log('Auto-refreshing CSRF token...');
        $.get('{{ route("admin.csrf.token") }}')
            .done(function(data) {
                console.log('Token auto-refreshed:', data.token);
                $('input[name="_token"]').val(data.token);
                $('meta[name="csrf-token"]').attr('content', data.token);
            })
            .fail(function() {
                console.error('Failed to auto-refresh CSRF token');
            });
    }, 5 * 60 * 1000); // 5 minutes for testing
});
</script>
@endsection
