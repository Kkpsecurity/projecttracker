@extends('adminlte::page')

@section('title', 'User Management - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">User Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Admin Center</a></li>
                <li class="breadcrumb-item active">User Management</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['total_users'] ?? 0 }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['active_users'] ?? 0 }}</h3>
                        <p>Active Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['new_users_this_month'] ?? 0 }}</h3>
                        <p>New This Month</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $stats['users_with_2fa'] ?? 0 }}</h3>
                        <p>2FA Enabled</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    User Management
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>
                        Add New User
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Quick Search -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <input type="text" id="search-input" class="form-control" placeholder="Search users...">
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <form id="bulk-action-form" method="POST" action="{{ route('admin.users.bulk-action') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select name="bulk_action" id="bulk_action" class="form-control">
                                    <option value="">Select Bulk Action...</option>
                                    <option value="reset_password">Reset Password</option>
                                    <option value="send_verification">Send Email Verification</option>
                                    <option value="disable_2fa">Disable 2FA</option>
                                    <option value="delete">Delete Users</option>
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-warning" onclick="return confirmBulkAction()">
                                        <i class="fas fa-bolt mr-1"></i>
                                        Execute
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <span id="selected-count" class="text-muted">0 users selected</span>
                        </div>
                    </div>

                    <!-- Users DataTable -->
                    <div class="table-responsive">
                        <table id="users-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>2FA</th>
                                    <th>Last Login</th>
                                    <th>Login Count</th>
                                    <th>Created</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                               class="form-check-input user-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar mr-2">
                                                <img src="{{ $user->adminlte_image() }}"
                                                     alt="{{ $user->name }}"
                                                     class="img-circle elevation-2"
                                                     style="width: 30px; height: 30px;">
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->id == auth()->id())
                                                    <span class="badge badge-primary badge-sm ml-1">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                        @if($user->email_verified_at)
                                            <i class="fas fa-check-circle text-success ml-1" title="Email Verified"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-warning ml-1" title="Email Not Verified"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active ?? true)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->two_factor_enabled)
                                            <span class="badge badge-success">
                                                <i class="fas fa-shield-alt mr-1"></i>
                                                Enabled
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Disabled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->last_login_at)
                                            <span title="{{ $user->last_login_at->format('M d, Y H:i:s') }}">
                                                {{ $user->last_login_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $user->login_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span title="{{ $user->created_at->format('M d, Y H:i:s') }}">
                                            {{ $user->created_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="resetPassword({{ $user->id }})">
                                                        <i class="fas fa-key mr-1"></i>
                                                        Reset Password
                                                    </a>
                                                    @if(!$user->email_verified_at)
                                                    <a class="dropdown-item" href="#" onclick="toggleEmailVerification({{ $user->id }})">
                                                        <i class="fas fa-envelope-check mr-1"></i>
                                                        Verify Email
                                                    </a>
                                                    @endif
                                                    @if($user->two_factor_enabled)
                                                    <a class="dropdown-item" href="#" onclick="disableTwoFactor({{ $user->id }})">
                                                        <i class="fas fa-shield-alt mr-1"></i>
                                                        Disable 2FA
                                                    </a>
                                                    @endif
                                                    <div class="dropdown-divider"></div>
                                                    @if($user->id != auth()->id())
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteUser({{ $user->id }})">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Delete User
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p class="lead">No users found</p>
                                            <p>Try adjusting your search criteria or <a href="{{ route('admin.users.create') }}">create a new user</a>.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Pagination -->
                @if($users->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $users->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.user-avatar img {
    object-fit: cover;
}
.small-box {
    border-radius: 0.375rem;
}
.card-primary.card-outline {
    border-top: 3px solid #007bff;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Select all functionality
    $('#select-all').change(function() {
        $('.user-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });

    $('.user-checkbox').change(function() {
        updateSelectedCount();

        // Update select all checkbox
        var totalCheckboxes = $('.user-checkbox').length;
        var checkedCheckboxes = $('.user-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    function updateSelectedCount() {
        var count = $('.user-checkbox:checked').length;
        $('#selected-count').text(count + ' user' + (count !== 1 ? 's' : '') + ' selected');
    }
});

function confirmBulkAction() {
    var selectedUsers = $('.user-checkbox:checked').length;
    var action = $('#bulk_action').val();

    if (!action) {
        alert('Please select a bulk action.');
        return false;
    }

    if (selectedUsers === 0) {
        alert('Please select at least one user.');
        return false;
    }

    var actionText = $('#bulk_action option:selected').text();
    return confirm('Are you sure you want to ' + actionText.toLowerCase() + ' for ' + selectedUsers + ' user(s)?');
}

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
                alert('Error resetting password: ' + xhr.responseJSON.message);
            }
        });
    }
}

function toggleEmailVerification(userId) {
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
            alert('Error updating email verification: ' + xhr.responseJSON.message);
        }
    });
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
                alert('Error disabling 2FA: ' + xhr.responseJSON.message);
            }
        });
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        $.ajax({
            url: '/admin/users/' + userId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error deleting user: ' + xhr.responseJSON.message);
            }
        });
    }
}
</script>
@stop
