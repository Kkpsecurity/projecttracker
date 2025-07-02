@extends('adminlte::page')

@section('title', 'Field Configuration Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Field Configuration: {{ $hb837ImportConfig->field_label }}</h1>
        <div>
            @if($hb837ImportConfig->canBeModified())
                <a href="{{ route('admin.hb837-import-config.edit', $hb837ImportConfig) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
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

    <div class="row">
        <div class="col-md-8">
            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Database Field:</dt>
                        <dd class="col-sm-9">
                            <code>{{ $hb837ImportConfig->database_field }}</code>
                            @if($hb837ImportConfig->is_system_field)
                                <span class="badge badge-warning ml-1">System Field</span>
                            @endif
                            @if($hb837ImportConfig->is_foreign_key)
                                <span class="badge badge-info ml-1">Foreign Key</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Field Label:</dt>
                        <dd class="col-sm-9">{{ $hb837ImportConfig->field_label }}</dd>

                        @if($hb837ImportConfig->description)
                        <dt class="col-sm-3">Description:</dt>
                        <dd class="col-sm-9">{{ $hb837ImportConfig->description }}</dd>
                        @endif

                        <dt class="col-sm-3">Field Type:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-secondary">{{ $hb837ImportConfig->field_type }}</span>
                            @if($hb837ImportConfig->max_length)
                                <small class="text-muted">(max: {{ $hb837ImportConfig->max_length }})</small>
                            @endif
                        </dd>

                        @if($hb837ImportConfig->default_value)
                        <dt class="col-sm-3">Default Value:</dt>
                        <dd class="col-sm-9"><code>{{ $hb837ImportConfig->default_value }}</code></dd>
                        @endif

                        @if($hb837ImportConfig->is_foreign_key && $hb837ImportConfig->foreign_table)
                        <dt class="col-sm-3">Foreign Reference:</dt>
                        <dd class="col-sm-9">
                            <code>{{ $hb837ImportConfig->foreign_table }}.{{ $hb837ImportConfig->foreign_key_column ?? 'id' }}</code>
                        </dd>
                        @endif

                        <dt class="col-sm-3">Sort Order:</dt>
                        <dd class="col-sm-9">{{ $hb837ImportConfig->sort_order }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Excel Column Mapping -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Excel Column Mapping</h3>
                </div>
                <div class="card-body">
                    @if($hb837ImportConfig->excel_column_mappings && count($hb837ImportConfig->excel_column_mappings) > 0)
                        <p>This field will match any of the following Excel column headers:</p>
                        <ul class="list-unstyled">
                            @foreach($hb837ImportConfig->excel_column_mappings as $mapping)
                                <li><span class="badge badge-light">{{ $mapping }}</span></li>
                            @endforeach
                        </ul>
                    @else
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
