@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1>Create New User</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Error Messages --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="bio">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror"
                                              id="bio" name="bio" rows="3">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_admin">Administrator</label>
                                    </div>
                                    <small class="text-muted">Administrators have full access to the system.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Active Account</label>
                                    </div>
                                    <small class="text-muted">Inactive users cannot log in.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Form validation and submission handling
    $('form').on('submit', function(e) {
        var isValid = true;
        var firstError = null;

        // Clear previous error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Validate required fields
        $('input[required], select[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                if (!$(this).siblings('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">This field is required.</div>');
                }
                isValid = false;
                if (!firstError) firstError = $(this);
            }
        });

        // Validate email format
        var emailField = $('input[type="email"]');
        if (emailField.val()) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.val())) {
                emailField.addClass('is-invalid');
                if (!emailField.siblings('.invalid-feedback').length) {
                    emailField.after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                }
                isValid = false;
                if (!firstError) firstError = emailField;
            }
        }

        // Validate password confirmation
        var password = $('#password').val();
        var passwordConfirm = $('#password_confirmation').val();
        if (password && password !== passwordConfirm) {
            $('#password_confirmation').addClass('is-invalid');
            if (!$('#password_confirmation').siblings('.invalid-feedback').length) {
                $('#password_confirmation').after('<div class="invalid-feedback">Passwords do not match.</div>');
            }
            isValid = false;
            if (!firstError) firstError = $('#password_confirmation');
        }

        if (!isValid) {
            e.preventDefault();
            if (firstError) {
                firstError.focus();
            }
            return false;
        }

        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating User...');
    });

    // Real-time validation
    $('input[required]').on('blur', function() {
        if (!$(this).val().trim()) {
            $(this).addClass('is-invalid');
            if (!$(this).siblings('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">This field is required.</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Email validation on input
    $('input[type="email"]').on('input', function() {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if ($(this).val() && !emailRegex.test($(this).val())) {
            $(this).addClass('is-invalid');
            if (!$(this).siblings('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Please enter a valid email address.</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });

    // Password confirmation validation
    $('#password_confirmation').on('input', function() {
        var password = $('#password').val();
        var passwordConfirm = $(this).val();
        if (password && passwordConfirm && password !== passwordConfirm) {
            $(this).addClass('is-invalid');
            if (!$(this).siblings('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Passwords do not match.</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });
});
</script>
@stop
