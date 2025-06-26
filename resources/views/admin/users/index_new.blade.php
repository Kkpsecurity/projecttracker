@extends('layouts.admin')

@section('title', 'User Management')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">User Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    <div class="row">
        <div class="col-12">
            <div class="card card-table">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="card-title">
                                <i class="fas fa-users"></i>
                                System Users
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-light">
                                <i class="fas fa-user-plus"></i> Create New User
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive-modern">
                        <table class="table table-modern table-compact" id="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th style="width: 100px;">Role</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 120px;">Created</th>
                                    <th style="width: 120px;">Last Login</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td><span class="table-id">#{{ $user->id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if($user->id == 1)
                                                        <small class="table-badge badge-danger ml-1">SUPER</small>
                                                    @elseif($user->id == 2)
                                                        <small class="table-badge badge-warning ml-1">ADMIN</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $user->email }}" class="table-email">{{ $user->email }}</a>
                                        </td>
                                        <td>
                                            @if($user->id == 1)
                                                <span class="table-badge badge-danger">Super Admin</span>
                                            @elseif($user->id == 2)
                                                <span class="table-badge badge-warning">Admin</span>
                                            @else
                                                <span class="table-badge badge-info">User</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->email_verified_at)
                                                <span class="table-badge badge-success">Active</span>
                                            @else
                                                <span class="table-badge badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="table-date">
                                                {{ $user->created_at->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="table-date">
                                                {{ $user->updated_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="table-actions">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                                   class="btn btn-info btn-sm" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id != 1 && $user->id != auth()->id())
                                                    <form action="{{ route('admin.users.delete', $user->id) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm btn-delete" 
                                                                title="Delete User">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-secondary btn-sm" disabled title="Cannot delete this user">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <br>
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($users->hasPages())
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="row mt-3">
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Users</span>
                    <span class="info-box-number">{{ $users->total() }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Users</span>
                    <span class="info-box-number">{{ $users->where('email_verified_at', '!=', null)->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-user-shield"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Admins</span>
                    <span class="info-box-number">{{ $users->whereIn('id', [1, 2])->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-user-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number">{{ $users->where('email_verified_at', null)->count() }}</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('custom_css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <style>
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .btn-group-sm > .btn {
            margin: 0 1px;
        }
        
        .info-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        
        .user-panel .info {
            display: block;
        }
    </style>
@stop

@section('custom_js')
    <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#users-table').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": false, // Disable DataTable pagination since we're using Laravel pagination
                "order": [[ 0, "asc" ]], // Sort by ID by default
                "columnDefs": [
                    { "orderable": false, "targets": 7 } // Disable ordering on actions column
                ]
            });
            
            // Confirm deletion
            $(document).on('click', '.btn-delete', function(e) {
                const userName = $(this).closest('tr').find('td:nth-child(2) strong').text();
                if (!confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
@stop
