@extends('adminlte::page')

@section('title', 'User Details')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1>User Details: {{ $user->name }}</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- User Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Full Name:</dt>
                        <dd class="col-sm-9">{{ $user->name }}</dd>

                        <dt class="col-sm-3">Email:</dt>
                        <dd class="col-sm-9">{{ $user->email }}</dd>

                        <dt class="col-sm-3">Phone:</dt>
                        <dd class="col-sm-9">{{ $user->phone ?? 'Not provided' }}</dd>

                        <dt class="col-sm-3">Bio:</dt>
                        <dd class="col-sm-9">{{ $user->bio ?? 'No bio provided' }}</dd>

                        <dt class="col-sm-3">Role:</dt>
                        <dd class="col-sm-9">
                            @if($user->is_admin)
                                <span class="badge badge-danger">Administrator</span>
                            @else
                                <span class="badge badge-secondary">User</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            @if($user->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-warning">Inactive</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Email Verified:</dt>
                        <dd class="col-sm-9">
                            @if($user->email_verified)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Verified
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-times"></i> Not Verified
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Two-Factor Auth:</dt>
                        <dd class="col-sm-9">
                            @if($user->two_factor_enabled)
                                <span class="badge badge-success">
                                    <i class="fas fa-shield-alt"></i> Enabled
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-shield-alt"></i> Disabled
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Member Since:</dt>
                        <dd class="col-sm-9">{{ $user->created_at->format('F j, Y') }}</dd>

                        <dt class="col-sm-3">Last Updated:</dt>
                        <dd class="col-sm-9">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</dd>

                        <dt class="col-sm-3">Last Login:</dt>
                        <dd class="col-sm-9">{{ $user->last_login_at ? $user->last_login_at->format('F j, Y \a\t g:i A') : 'Never logged in' }}</dd>

                        @if($user->password_changed_at)
                            <dt class="col-sm-3">Password Changed:</dt>
                            <dd class="col-sm-9">{{ $user->password_changed_at->format('F j, Y \a\t g:i A') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- User Avatar & Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Avatar</h3>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="img-circle" width="100" height="100">
                    @else
                        <div class="bg-secondary img-circle d-inline-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif
                    <p class="mt-3">{{ $user->name }}</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100" role="group">
                        <button type="button" class="btn btn-warning mb-2" onclick="resetPassword()">
                            <i class="fas fa-key"></i> Reset Password
                        </button>

                        @if($user->email_verified)
                            <button type="button" class="btn btn-secondary mb-2" onclick="toggleEmailVerification()">
                                <i class="fas fa-times"></i> Unverify Email
                            </button>
                        @else
                            <button type="button" class="btn btn-success mb-2" onclick="toggleEmailVerification()">
                                <i class="fas fa-check"></i> Verify Email
                            </button>
                        @endif

                        @if($user->two_factor_enabled)
                            <button type="button" class="btn btn-danger mb-2" onclick="disableTwoFactor()">
                                <i class="fas fa-shield-alt"></i> Disable 2FA
                            </button>
                        @endif

                        @if($user->id !== auth()->id())
                            <button type="button" class="btn btn-danger" onclick="deleteUser()">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="passwordForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset Password</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function resetPassword() {
    $('#passwordModal').modal('show');
}

$('#passwordForm').submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: '{{ route("admin.users.reset-password", $user) }}',
        type: 'PATCH',
        data: $(this).serialize(),
        success: function(result) {
            alert('Password reset successfully!');
            $('#passwordModal').modal('hide');
            $('#passwordForm')[0].reset();
        },
        error: function(xhr) {
            alert('Error resetting password: ' + xhr.responseText);
        }
    });
});

function toggleEmailVerification() {
    $.ajax({
        url: '{{ route("admin.users.toggle-email-verification", $user) }}',
        type: 'PATCH',
        success: function(result) {
            alert(result.message);
            location.reload();
        },
        error: function(xhr) {
            alert('Error updating email verification: ' + xhr.responseText);
        }
    });
}

function disableTwoFactor() {
    if (confirm('Are you sure you want to disable two-factor authentication for this user?')) {
        $.ajax({
            url: '{{ route("admin.users.disable-two-factor", $user) }}',
            type: 'PATCH',
            success: function(result) {
                alert(result.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Error disabling two-factor authentication: ' + xhr.responseText);
            }
        });
    }
}

function deleteUser() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        $.ajax({
            url: '{{ route("admin.users.destroy", $user) }}',
            type: 'DELETE',
            success: function(result) {
                alert('User deleted successfully!');
                window.location.href = '{{ route("admin.users.index") }}';
            },
            error: function(xhr) {
                alert('Error deleting user: ' + xhr.responseText);
            }
        });
    }
}
</script>
@stop
