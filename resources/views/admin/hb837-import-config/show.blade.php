@extends('adminlte::page')

@section('title', 'Field Details - ' . $fieldDetails['database_field'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Field Details: <code>{{ $fieldDetails['database_field'] }}</code></h1>
        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Field Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Database Field:</dt>
                        <dd class="col-sm-9"><code>{{ $fieldDetails['database_field'] }}</code></dd>
                        
                        <dt class="col-sm-3">Excel Columns:</dt>
                        <dd class="col-sm-9">
                            @if($fieldDetails['excel_columns'])
                                @foreach($fieldDetails['excel_columns'] as $column)
                                    <span class="badge badge-info">{{ $column }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Column Exists:</dt>
                        <dd class="col-sm-9">
                            @if($fieldDetails['column_exists'])
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Yes
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times"></i> No
                                </span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Column Type:</dt>
                        <dd class="col-sm-9">{{ $fieldDetails['column_type'] }}</dd>
                        
                        <dt class="col-sm-3">Field Type:</dt>
                        <dd class="col-sm-9">
                            @if($fieldDetails['is_system_field'])
                                <span class="badge badge-warning">System Field</span>
                            @endif
                            @if($fieldDetails['is_foreign_key'])
                                <span class="badge badge-info">Foreign Key</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical btn-block">
                        <a href="{{ route('admin.hb837-import-config.edit', $fieldDetails['database_field']) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Mapping
                        </a>
                        
                        @if(!$fieldDetails['column_exists'])
                            <form method="POST" action="{{ route('admin.hb837-import-config.create-column', $fieldDetails['database_field']) }}" 
                                  onsubmit="return confirm('Create database column for this field?')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-plus"></i> Create Database Column
                                </button>
                            </form>
                        @endif
                        
                        @if(!$fieldDetails['is_system_field'])
                            <form method="POST" action="{{ route('admin.hb837-import-config.destroy', $fieldDetails['database_field']) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this field mapping?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Delete Mapping
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
                        <p class="text-muted">No Excel column mappings defined.</p>
                    @endif
                </div>
            </div>

            <!-- Validation & Transformation -->
            @if($hb837ImportConfig->validation_rules || $hb837ImportConfig->transformation_type || $hb837ImportConfig->enum_values)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Validation & Transformation</h3>
                </div>
                <div class="card-body">
                    @if($hb837ImportConfig->validation_rules)
                    <h6>Validation Rules:</h6>
                    <ul class="list-unstyled">
                        @foreach($hb837ImportConfig->validation_rules as $rule)
                            <li><code>{{ $rule }}</code></li>
                        @endforeach
                    </ul>
                    @endif

                    @if($hb837ImportConfig->transformation_type)
                    <h6>Data Transformation:</h6>
                    <p><span class="badge badge-info">{{ $hb837ImportConfig->transformation_type }}</span></p>

                    @if($hb837ImportConfig->transformation_options)
                        <h6>Transformation Options:</h6>
                        <dl class="row">
                            @foreach($hb837ImportConfig->transformation_options as $key => $value)
                                <dt class="col-sm-4">{{ $key }}:</dt>
                                <dd class="col-sm-8">{{ $value }}</dd>
                            @endforeach
                        </dl>
                    @endif
                    @endif

                    @if($hb837ImportConfig->enum_values)
                    <h6>Allowed Values (Enum):</h6>
                    <div>
                        @foreach($hb837ImportConfig->enum_values as $value)
                            <span class="badge badge-outline-primary mr-1">{{ $value }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Status & Options -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status & Options</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Active:</dt>
                        <dd class="col-sm-6">
                            @if($hb837ImportConfig->is_active)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6">Nullable:</dt>
                        <dd class="col-sm-6">
                            @if($hb837ImportConfig->nullable)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6">Required for Import:</dt>
                        <dd class="col-sm-6">
                            @if($hb837ImportConfig->is_required_for_import)
                                <span class="badge badge-warning">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6">Updatable:</dt>
                        <dd class="col-sm-6">
                            @if($hb837ImportConfig->is_updatable)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6">Creatable:</dt>
                        <dd class="col-sm-6">
                            @if($hb837ImportConfig->is_creatable)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6">Can Be Modified:</dt>
                        <dd class="col-sm-6">
                            @if($hb837ImportConfig->canBeModified())
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6">Can Be Deleted:</dt>
                        <dd class="col-sm-6">
                            @if($hb837ImportConfig->canBeDeleted())
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-danger">No</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Database Column Status -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Database Column Status</h3>
                </div>
                <div class="card-body">
                    @if($columnExists)
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> Database column exists
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Database column does not exist
                            @if(!$hb837ImportConfig->is_system_field)
                                <br><br>
                                <a href="{{ route('admin.hb837-import-config.create-column', $hb837ImportConfig) }}"
                                   class="btn btn-sm btn-primary"
                                   onclick="return confirm('Create database column for this field?')">
                                    <i class="fas fa-plus"></i> Create Column
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    @if($hb837ImportConfig->canBeModified())
                        <a href="{{ route('admin.hb837-import-config.edit', $hb837ImportConfig) }}"
                           class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> Edit Configuration
                        </a>
                    @endif

                    @if(!$columnExists && !$hb837ImportConfig->is_system_field)
                        <a href="{{ route('admin.hb837-import-config.create-column', $hb837ImportConfig) }}"
                           class="btn btn-success btn-block mt-2"
                           onclick="return confirm('Create database column for this field?')">
                            <i class="fas fa-plus"></i> Create Database Column
                        </a>
                    @endif

                    @if($hb837ImportConfig->canBeDeleted())
                        <form method="POST" action="{{ route('admin.hb837-import-config.destroy', $hb837ImportConfig) }}"
                              onsubmit="return confirm('Are you sure you want to delete this field? This will also remove the database column if it exists.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block mt-2">
                                <i class="fas fa-trash"></i> Delete Configuration
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            @if($hb837ImportConfig->created_at)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Metadata</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Created:</dt>
                        <dd class="col-sm-8">{{ $hb837ImportConfig->created_at->format('M j, Y g:i A') }}</dd>

                        @if($hb837ImportConfig->updated_at && $hb837ImportConfig->updated_at != $hb837ImportConfig->created_at)
                        <dt class="col-sm-4">Last Updated:</dt>
                        <dd class="col-sm-8">{{ $hb837ImportConfig->updated_at->format('M j, Y g:i A') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

@section('css')
<style>
    .badge-outline-primary {
        color: #007bff;
        border: 1px solid #007bff;
        background: transparent;
    }
</style>
@stop
