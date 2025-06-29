@extends('adminlte::page')

@section('title', 'User Management')

@section('content_header')
    <div class="row">
        <div class="col-md-6">
            <h1>User Management</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Search and Filter Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search & Filter</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Search Users</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                           value="{{ request('search') }}" placeholder="Search by name, email, or phone">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Users</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="admin" {{ request('status') == 'admin' ? 'selected' : '' }}>Admins</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sort_by">Sort By</label>
                                    <select class="form-control" id="sort_by" name="sort_by">
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                                        <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="last_login_at" {{ request('sort_by') == 'last_login_at' ? 'selected' : '' }}>Last Login</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users List</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-danger" id="bulk-delete" disabled>
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="bulk-activate" disabled>
                            <i class="fas fa-check"></i> Activate Selected
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="bulk-deactivate" disabled>
                            <i class="fas fa-ban"></i> Deactivate Selected
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                                    </td>
                                    <td>
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" class="img-circle" width="30" height="30">
                                        @else
                                            <div class="bg-secondary img-circle d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge badge-danger">Admin</span>
                                        @else
                                            <span class="badge badge-secondary">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <button type="button" class="btn btn-danger btn-sm delete-user"
                                                        data-user-id="{{ $user->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Select all checkbox functionality
    $('#select-all').change(function() {
        $('.user-checkbox').prop('checked', this.checked);
        toggleBulkButtons();
    });

    // Individual checkbox change
    $('.user-checkbox').change(function() {
        toggleBulkButtons();
    });

    // Toggle bulk action buttons
    function toggleBulkButtons() {
        const checkedCount = $('.user-checkbox:checked').length;
        $('#bulk-delete, #bulk-activate, #bulk-deactivate').prop('disabled', checkedCount === 0);
    }

    // Delete user functionality
    let deleteUserId = null;

    $('.delete-user').click(function() {
        deleteUserId = $(this).data('user-id');
        $('#deleteModal').modal('show');
    });

    $('#confirm-delete').click(function() {
        if (deleteUserId) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/admin/users/' + deleteUserId,
                type: 'DELETE',
                success: function(result) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error deleting user: ' + xhr.responseText);
                }
            });
        }
        $('#deleteModal').modal('hide');
    });

    // Bulk actions
    $('#bulk-delete').click(function() {
        performBulkAction('delete');
    });

    $('#bulk-activate').click(function() {
        performBulkAction('activate');
    });

    $('#bulk-deactivate').click(function() {
        performBulkAction('deactivate');
    });

    function performBulkAction(action) {
        const userIds = $('.user-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (userIds.length === 0) {
            alert('Please select at least one user.');
            return;
        }

        if (action === 'delete' && !confirm('Are you sure you want to delete the selected users?')) {
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/admin/users/bulk-action',
            type: 'POST',
            data: {
                action: action,
                user_ids: userIds
            },
            success: function(result) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error performing bulk action: ' + xhr.responseText);
            }
        });
    }
});
</script>
@stop
