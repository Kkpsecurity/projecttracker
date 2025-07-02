@extends('adminlte::page')

@section('title', 'HB837 Import Configuration')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>HB837 Import Field Configuration</h1>
        <div>
            <a href="{{ route('admin.hb837-import-config.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Field
            </a>
            <div class="btn-group ml-2">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-cog"></i> Actions
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.hb837-import-config.sync') }}">
                        <i class="fas fa-sync"></i> Sync Configuration File
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.hb837-import-config.import-schema') }}">
                        <i class="fas fa-download"></i> Import from Database Schema
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Config Fields (Immutable) -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cog"></i> Default Configuration Fields
            </h3>
            <div class="card-tools">
                <span class="badge badge-secondary">{{ $configFields->count() }} config fields</span>
                <span class="text-muted ml-2">
                    <i class="fas fa-lock"></i> Read-only (labels can be updated)
                </span>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Database Field</th>
                        <th>Label</th>
                        <th>Type</th>
                        <th>Excel Mappings</th>
                        <th>Status</th>
                        <th>DB Column</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($configFields as $field)
                        <tr class="{{ !$field->is_active ? 'text-muted' : '' }}">
                            <td>
                                <span class="badge badge-secondary">{{ $field->sort_order }}</span>
                            </td>
                            <td>
                                <code>{{ $field->database_field }}</code>
                                @if($field->is_system_field)
                                    <span class="badge badge-warning badge-sm ml-1" title="System Field">SYS</span>
                                @endif
                                @if($field->is_foreign_key)
                                    <span class="badge badge-info badge-sm ml-1" title="Foreign Key">FK</span>
                                @endif
                                <span class="badge badge-secondary badge-sm ml-1" title="Config Field">CFG</span>
                            </td>
                            <td>{{ $field->field_label }}</td>
                            <td>
                                <span class="badge badge-outline-secondary">{{ $field->field_type }}</span>
                                @if($field->max_length)
                                    <small class="text-muted">({{ $field->max_length }})</small>
                                @endif
                            </td>
                            <td>
                                @if($field->excel_column_mappings)
                                    <small>
                                        {{ implode(', ', array_slice($field->excel_column_mappings, 0, 3)) }}
                                        @if(count($field->excel_column_mappings) > 3)
                                            <span class="text-muted">+{{ count($field->excel_column_mappings) - 3 }} more</span>
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                @if($field->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif

                                @if($field->is_required_for_import)
                                    <span class="badge badge-warning badge-sm ml-1">Required</span>
                                @endif
                            </td>
                            <td>
                                @if($field->columnExists())
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Exists
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times"></i> Missing
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.hb837-import-config.show', $field) }}"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Config fields can only have labels edited -->
                                    <a href="{{ route('admin.hb837-import-config.edit', $field) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Edit label only">
                                        <i class="fas fa-tag"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No configuration fields found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Fields (Manageable) -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-wrench"></i> Custom Fields
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $customFields->count() }} custom fields</span>
                <span class="text-muted ml-2">
                    <i class="fas fa-unlock"></i> Fully manageable
                </span>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Database Field</th>
                        <th>Label</th>
                        <th>Type</th>
                        <th>Excel Mappings</th>
                        <th>Status</th>
                        <th>DB Column</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customFields as $field)
                        <tr class="{{ !$field->is_active ? 'text-muted' : '' }}">
                            <td>
                                <span class="badge badge-primary">{{ $field->sort_order }}</span>
                            </td>
                            <td>
                                <code>{{ $field->database_field }}</code>
                                @if($field->is_foreign_key)
                                    <span class="badge badge-info badge-sm ml-1" title="Foreign Key">FK</span>
                                @endif
                                <span class="badge badge-success badge-sm ml-1" title="Custom Field">CUSTOM</span>
                            </td>
                            <td>{{ $field->field_label }}</td>
                            <td>
                                <span class="badge badge-outline-primary">{{ $field->field_type }}</span>
                                @if($field->max_length)
                                    <small class="text-muted">({{ $field->max_length }})</small>
                                @endif
                            </td>
                            <td>
                                @if($field->excel_column_mappings)
                                    <small>
                                        {{ implode(', ', array_slice($field->excel_column_mappings, 0, 3)) }}
                                        @if(count($field->excel_column_mappings) > 3)
                                            <span class="text-muted">+{{ count($field->excel_column_mappings) - 3 }} more</span>
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                @if($field->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif

                                @if($field->is_required_for_import)
                                    <span class="badge badge-warning badge-sm ml-1">Required</span>
                                @endif
                            </td>
                            <td>
                                @if($field->columnExists())
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Exists
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times"></i> Missing
                                    </span>
                                    <a href="{{ route('admin.hb837-import-config.create-column', $field) }}"
                                       class="btn btn-xs btn-outline-primary ml-1"
                                       onclick="return confirm('Create database column for this field?')">
                                        Create
                                    </a>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.hb837-import-config.show', $field) }}"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.hb837-import-config.edit', $field) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.hb837-import-config.destroy', $field) }}"
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this field? This will also remove the database column if it exists.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No custom fields found.
                                <a href="{{ route('admin.hb837-import-config.create') }}" class="btn btn-sm btn-primary ml-2">
                                    <i class="fas fa-plus"></i> Create First Custom Field
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Field Types Legend</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">CFG</dt>
                        <dd class="col-sm-9">Configuration field - structure is immutable, labels can be updated</dd>
                        <dt class="col-sm-3">CUSTOM</dt>
                        <dd class="col-sm-9">Custom field - fully manageable (create, edit, delete)</dd>
                        <dt class="col-sm-3">SYS</dt>
                        <dd class="col-sm-9">System field - cannot be modified or deleted</dd>
                        <dt class="col-sm-3">FK</dt>
                        <dd class="col-sm-9">Foreign key - references another table</dd>
                        <dt class="col-sm-3">Required</dt>
                        <dd class="col-sm-9">Required for import process</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Stats</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Config Fields:</dt>
                        <dd class="col-sm-6">{{ $configFields->count() }}</dd>
                        <dt class="col-sm-6">Custom Fields:</dt>
                        <dd class="col-sm-6">{{ $customFields->count() }}</dd>
                        <dt class="col-sm-6">Active Fields:</dt>
                        <dd class="col-sm-6">{{ $configFields->where('is_active', true)->count() + $customFields->where('is_active', true)->count() }}</dd>
                        <dt class="col-sm-6">Total Fields:</dt>
                        <dd class="col-sm-6">{{ $configFields->count() + $customFields->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .badge-outline-secondary {
        color: #6c757d;
        border: 1px solid #6c757d;
        background: transparent;
    }
</style>
@stop
