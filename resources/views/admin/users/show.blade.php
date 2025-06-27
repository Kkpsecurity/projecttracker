@extends('layouts.admin')

@section('title', 'User Details')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">User Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">{{ $user->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i>
                        {{ $user->name }}
                        @if($user->id == 1)
                            <span class="badge badge-danger ml-2">Super Admin</span>
                        @elseif($user->id == 2)
                            <span class="badge badge-warning ml-2">Admin</span>
                        @else
                            <span class="badge badge-info ml-2">User</span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-info-circle text-info"></i> Basic Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 30%;">Full Name:</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email Address:</th>
                                    <td>
                                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>User ID:</th>
                                    <td>#{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Account Status:</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge badge-success">Active & Verified</span>
                                        @else
                                            <span class="badge badge-secondary">Pending Verification</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-clock text-primary"></i> Account Timeline</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 30%;">Created:</th>
                                    <td>
                                        {{ $user->created_at->format('M d, Y \a\t g:i A') }}
                                        <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>
                                        {{ $user->updated_at->format('M d, Y \a\t g:i A') }}
                                        <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                                    </td>
                                </tr>
                                @if($user->email_verified_at)
                                <tr>
                                    <th>Email Verified:</th>
                                    <td>
                                        {{ $user->email_verified_at->format('M d, Y \a\t g:i A') }}
                                        <small class="text-muted">({{ $user->email_verified_at->diffForHumans() }})</small>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        
                        @if(!$user->email_verified_at)
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="email_verified" value="1">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check"></i> Mark Email as Verified
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($user->id != 1 && $user->id != auth()->id())
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i>
                            Danger Zone
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Permanently delete this user account. This action cannot be undone.</p>
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop