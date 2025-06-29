{{-- KKP Security Project Tracker - Account Dashboard --}}
@extends('adminlte::page')

@section('title', 'Account Dashboard - KKP Security')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-user-cog text-primary mr-2"></i>
                    Account Dashboard
                </h1>
                <small class="text-muted">
                    Manage your account settings and security preferences
                </small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Account</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="row">
    {{-- Profile Information Card --}}
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>Profile Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Name:</strong></div>
                    <div class="col-sm-8">{{ $user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Email:</strong></div>
                    <div class="col-sm-8">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Account Created:</strong></div>
                    <div class="col-sm-8">{{ $stats['account_created']->format('M d, Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Last Login:</strong></div>
                    <div class="col-sm-8">{{ $stats['last_login']->format('M d, Y g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Account Statistics Card --}}
    <div class="col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>Account Statistics
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Total Logins:</strong></div>
                    <div class="col-sm-6">{{ number_format($stats['total_logins']) }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Active Sessions:</strong></div>
                    <div class="col-sm-6">{{ $stats['active_sessions'] }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Two-Factor Auth:</strong></div>
                    <div class="col-sm-6">
                        @if($stats['two_factor_enabled'])
                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Enabled</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-times mr-1"></i>Disabled</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>Email Verified:</strong></div>
                    <div class="col-sm-6">
                        @if($user->email_verified_at)
                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Verified</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Unverified</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Action Cards Row --}}
<div class="row">
    {{-- Update Name Card --}}
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-signature mr-2"></i>Update Name
                </h3>
            </div>
            <form action="{{ route('account.update.name') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="current_password_name">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                               id="current_password_name" name="current_password" required>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Update Name
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Update Email Card --}}
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-envelope mr-2"></i>Update Email
                </h3>
            </div>
            <form action="{{ route('account.update.email') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="current_password_email">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                               id="current_password_email" name="current_password" required>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Update Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Password & Security Row --}}
<div class="row">
    {{-- Change Password Card --}}
    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-key mr-2"></i>Change Password
                </h3>
            </div>
            <form action="{{ route('account.update.password') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    <div class="form-group">
                        <label for="current_password_change">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                               id="current_password_change" name="current_password" required>
                        @error('current_password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key mr-1"></i>Change Password
                    </button>
                    <small class="text-muted d-block mt-2">
                        Changing your password will log you out from all other devices.
                    </small>
                </div>
            </form>
        </div>
    </div>

    {{-- Security Settings Card --}}
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>Security Settings
                </h3>
            </div>
            <div class="card-body">
                {{-- Two-Factor Authentication --}}
                <div class="form-group">
                    <label>Two-Factor Authentication</label>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            @if($stats['two_factor_enabled'])
                                <span class="badge badge-success">Enabled</span>
                            @else
                                <span class="badge badge-secondary">Disabled</span>
                            @endif
                        </span>
                        @if($stats['two_factor_enabled'])
                            <form action="{{ route('account.two-factor.disable') }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="current_password" id="2fa_disable_password">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirm2FADisable()">
                                    Disable 2FA
                                </button>
                            </form>
                        @else
                            <form action="{{ route('account.two-factor.enable') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="current_password" id="2fa_enable_password">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="confirm2FAEnable()">
                                    Enable 2FA
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Active Sessions --}}
                <div class="form-group">
                    <label>Active Sessions</label>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ $stats['active_sessions'] }} active session(s)</span>
                        <a href="{{ route('account.sessions') }}" class="btn btn-sm btn-outline-info">
                            Manage Sessions
                        </a>
                    </div>
                </div>

                {{-- Quick Security Actions --}}
                <div class="form-group">
                    <label>Quick Actions</label>
                    <div class="btn-group d-block">
                        <form action="{{ route('account.sessions.revoke-others') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="current_password" id="logout_others_password">
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="confirmLogoutOthers()">
                                Logout Other Devices
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Danger Zone --}}
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Delete Account</h5>
                    Once you delete your account, all of its resources and data will be permanently deleted.
                    Before deleting your account, please download any data or information that you wish to retain.
                </div>
                <button type="button" class="btn btn-danger" onclick="confirmAccountDeletion()">
                    <i class="fas fa-trash mr-1"></i>Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Forms for Confirmations --}}
<form id="delete-account-form" action="{{ route('account.delete') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="current_password" id="delete_password">
    <input type="hidden" name="delete_confirmation" id="delete_confirmation">
</form>
@stop

@section('css')
<style>
.badge {
    font-size: 0.8em;
}
.card-outline {
    border-width: 2px;
}
</style>
@stop

@section('js')
<script>
function confirm2FAEnable() {
    Swal.fire({
        title: 'Enable Two-Factor Authentication',
        text: 'Please enter your current password to enable 2FA:',
        input: 'password',
        inputPlaceholder: 'Current Password',
        showCancelButton: true,
        confirmButtonText: 'Enable 2FA',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        inputValidator: (value) => {
            if (!value) return 'Password is required!';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('2fa_enable_password').value = result.value;
            document.querySelector('form[action="{{ route('account.two-factor.enable') }}"]').submit();
        }
    });
}

function confirm2FADisable() {
    Swal.fire({
        title: 'Disable Two-Factor Authentication',
        text: 'Please enter your current password to disable 2FA:',
        input: 'password',
        inputPlaceholder: 'Current Password',
        showCancelButton: true,
        confirmButtonText: 'Disable 2FA',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        inputValidator: (value) => {
            if (!value) return 'Password is required!';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('2fa_disable_password').value = result.value;
            document.querySelector('form[action="{{ route('account.two-factor.disable') }}"]').submit();
        }
    });
}

function confirmLogoutOthers() {
    Swal.fire({
        title: 'Logout from Other Devices',
        text: 'Please enter your current password to logout from all other devices:',
        input: 'password',
        inputPlaceholder: 'Current Password',
        showCancelButton: true,
        confirmButtonText: 'Logout Others',
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        inputValidator: (value) => {
            if (!value) return 'Password is required!';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout_others_password').value = result.value;
            document.querySelector('form[action="{{ route('account.sessions.revoke-others') }}"]').submit();
        }
    });
}

function confirmAccountDeletion() {
    Swal.fire({
        title: 'Delete Account',
        html: `
            <div class="text-left">
                <p class="text-danger"><strong>This action cannot be undone!</strong></p>
                <p>Please enter your current password and type <code>DELETE MY ACCOUNT</code> to confirm:</p>
                <input type="password" id="delete-password-input" class="form-control mb-2" placeholder="Current Password">
                <input type="text" id="delete-confirm-input" class="form-control" placeholder="Type: DELETE MY ACCOUNT">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Delete Account',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const password = document.getElementById('delete-password-input').value;
            const confirmation = document.getElementById('delete-confirm-input').value;

            if (!password) {
                Swal.showValidationMessage('Password is required');
                return false;
            }
            if (confirmation !== 'DELETE MY ACCOUNT') {
                Swal.showValidationMessage('Please type "DELETE MY ACCOUNT" exactly');
                return false;
            }

            return { password, confirmation };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete_password').value = result.value.password;
            document.getElementById('delete_confirmation').value = result.value.confirmation;
            document.getElementById('delete-account-form').submit();
        }
    });
}

// Show success/error messages
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif

@if(session('backup_code'))
    Swal.fire({
        icon: 'info',
        title: 'Two-Factor Authentication Enabled',
        html: '<p>Your backup recovery code is:</p><code class="h5">{{ session('backup_code') }}</code><p class="mt-2 text-warning"><small>Please save this code in a secure location.</small></p>',
        confirmButtonText: 'I\'ve Saved It'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        timer: 5000
    });
@endif
</script>
@stop
