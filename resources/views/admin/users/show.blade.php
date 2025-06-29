@extends('adminlte::page')

@section('title', 'View User - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">User Profile: {{ $user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Admin Center</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                <li class="breadcrumb-item active">View User</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Profile Card -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ $user->adminlte_image() }}"
                         alt="{{ $user->name }}"
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>

                <h3 class="profile-username text-center">
                    {{ $user->name }}
                    @if($user->id == auth()->id())
                        <span class="badge badge-primary badge-sm ml-1">You</span>
                    @endif
                </h3>

                <p class="text-muted text-center">{{ $user->email }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Login Count</b>
                        <a class="float-right">{{ $user->login_count ?? 0 }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Last Login</b>
                        <a class="float-right">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </a>
                    </li>
                    <li class="list-group-item">
                        <b>Member Since</b>
                        <a class="float-right">{{ $user->created_at->format('M d, Y') }}</a>
                    </li>
                </ul>

                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default btn-block">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Status Card -->
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Account Status
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-2">
                        <span class="text-bold">Account Status:</span>
                        @if($user->is_active ?? true)
                            <span class="badge badge-success float-right">Active</span>
                        @else
                            <span class="badge badge-danger float-right">Inactive</span>
                        @endif
                    </div>
                    <div class="col-12 mb-2">
                        <span class="text-bold">Email Status:</span>
                        @if($user->email_verified_at)
                            <span class="badge badge-success float-right">
                                <i class="fas fa-check mr-1"></i>
                                Verified
                            </span>
                        @else
                            <span class="badge badge-warning float-right">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Unverified
                            </span>
                        @endif
                    </div>
                    <div class="col-12 mb-2">
                        <span class="text-bold">Two-Factor Auth:</span>
                        @if($user->two_factor_enabled)
                            <span class="badge badge-success float-right">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Enabled
                            </span>
                        @else
                            <span class="badge badge-secondary float-right">Disabled</span>
                        @endif
                    </div>
                    <div class="col-12">
                        <span class="text-bold">Active Sessions:</span>
                        <span class="badge badge-info float-right">{{ $user->active_sessions ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-8">
        <!-- Account Information -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-1"></i>
                    Account Information
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Account Created</label>
                            <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y H:i:s') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Updated</label>
                            <input type="text" class="form-control" value="{{ $user->updated_at->format('M d, Y H:i:s') }}" readonly>
                        </div>
                    </div>
                </div>

                @if($user->admin_notes)
                <div class="form-group">
                    <label>Admin Notes</label>
                    <textarea class="form-control" rows="3" readonly>{{ $user->admin_notes }}</textarea>
                </div>
                @endif
            </div>
        </div>

        <!-- Activity Statistics -->
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Activity Statistics
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-sign-in-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Logins</span>
                                <span class="info-box-number">{{ $user->login_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-clock"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Last Login</span>
                                <span class="info-box-number" style="font-size: 14px;">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y') }}<br>
                                        <small>{{ $user->last_login_at->format('H:i:s') }}</small>
                                    @else
                                        Never
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-desktop"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Sessions</span>
                                <span class="info-box-number">{{ $user->active_sessions ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Days Since Joined</span>
                                <span class="info-box-number">{{ $user->created_at->diffInDays(now()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <button type="button" class="btn btn-info btn-block" onclick="resetPassword({{ $user->id }})">
                            <i class="fas fa-key mr-1"></i>
                            Reset Password
                        </button>
                    </div>
                    @if(!$user->email_verified_at)
                    <div class="col-md-4 mb-2">
                        <button type="button" class="btn btn-success btn-block" onclick="verifyEmail({{ $user->id }})">
                            <i class="fas fa-envelope-check mr-1"></i>
                            Verify Email
                        </button>
                    </div>
                    @endif
                    @if($user->two_factor_enabled)
                    <div class="col-md-4 mb-2">
                        <button type="button" class="btn btn-warning btn-block" onclick="disableTwoFactor({{ $user->id }})">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Disable 2FA
                        </button>
                    </div>
                    @endif
                    @if($user->id != auth()->id())
                    <div class="col-md-4 mb-2">
                        <button type="button" class="btn btn-danger btn-block" onclick="deleteUser({{ $user->id }})">
                            <i class="fas fa-trash mr-1"></i>
                            Delete User
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.profile-user-img {
    border: 3px solid #adb5bd;
    margin: 0 auto;
    padding: 3px;
}

.card-primary.card-outline {
    border-top: 3px solid #007bff;
}

.card-info.card-outline {
    border-top: 3px solid #17a2b8;
}

.card-success.card-outline {
    border-top: 3px solid #28a745;
}

.card-warning.card-outline {
    border-top: 3px solid #ffc107;
}

.info-box {
    margin-bottom: 1rem;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@stop

@section('js')
<script>
function resetPassword(userId) {
    if (confirm('Are you sure you want to reset this user\'s password? They will receive an email with a new temporary password.')) {
        $.ajax({
            url: '/admin/users/' + userId + '/reset-password',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Password reset successfully. The user will receive an email with their new temporary password.');
            },
            error: function(xhr) {
                alert('Error resetting password: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }
}

function verifyEmail(userId) {
    if (confirm('Are you sure you want to mark this user\'s email as verified?')) {
        $.ajax({
            url: '/admin/users/' + userId + '/toggle-email-verification',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating email verification: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }
}

function disableTwoFactor(userId) {
    if (confirm('Are you sure you want to disable two-factor authentication for this user?')) {
        $.ajax({
            url: '/admin/users/' + userId + '/disable-two-factor',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error disabling 2FA: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        if (confirm('This will permanently delete the user account and all associated data. Are you absolutely sure?')) {
            $.ajax({
                url: '/admin/users/' + userId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('User deleted successfully.');
                    window.location.href = '{{ route("admin.users.index") }}';
                },
                error: function(xhr) {
                    alert('Error deleting user: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    }
}
</script>
@stop
