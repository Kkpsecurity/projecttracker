@extends('adminlte::page')

@section('title', 'System Settings - KKP Security Project Tracker')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>System Settings</h1>
        <div>
            <span class="badge badge-{{ $settings->maintenance_mode ? 'danger' : 'success' }}">
                {{ $settings->maintenance_mode ? 'Maintenance Mode ON' : 'System Online' }}
            </span>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>Validation Errors:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Main Settings Form --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Company Information Card --}}
            <div class="col-lg-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-building"></i> Company Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="company_name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" 
                                   value="{{ old('company_name', $settings->company_name) }}" required>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="company_email">Company Email</label>
                            <input type="email" class="form-control @error('company_email') is-invalid @enderror" 
                                   id="company_email" name="company_email" 
                                   value="{{ old('company_email', $settings->company_email) }}">
                            @error('company_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="company_phone">Company Phone</label>
                            <input type="text" class="form-control @error('company_phone') is-invalid @enderror" 
                                   id="company_phone" name="company_phone" 
                                   value="{{ old('company_phone', $settings->company_phone) }}">
                            @error('company_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="company_address">Company Address</label>
                            <textarea class="form-control @error('company_address') is-invalid @enderror" 
                                      id="company_address" name="company_address" rows="3">{{ old('company_address', $settings->company_address) }}</textarea>
                            @error('company_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Brand & Appearance Card --}}
            <div class="col-lg-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-palette"></i> Brand & Appearance</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="primary_color">Primary Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control @error('primary_color') is-invalid @enderror" 
                                       id="primary_color" name="primary_color" 
                                       value="{{ old('primary_color', $settings->primary_color ?? '#007bff') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ old('primary_color', $settings->primary_color ?? '#007bff') }}</span>
                                </div>
                            </div>
                            @error('primary_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="secondary_color">Secondary Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control @error('secondary_color') is-invalid @enderror" 
                                       id="secondary_color" name="secondary_color" 
                                       value="{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ old('secondary_color', $settings->secondary_color ?? '#6c757d') }}</span>
                                </div>
                            </div>
                            @error('secondary_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="site_logo_url">Site Logo URL</label>
                            <input type="url" class="form-control @error('site_logo_url') is-invalid @enderror" 
                                   id="site_logo_url" name="site_logo_url" 
                                   value="{{ old('site_logo_url', $settings->site_logo_url) }}"
                                   placeholder="https://example.com/logo.png">
                            @error('site_logo_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Or upload a file below</small>
                        </div>

                        <div class="form-group">
                            <label for="logo_file">Upload Logo File</label>
                            <input type="file" class="form-control-file @error('logo_file') is-invalid @enderror" 
                                   id="logo_file" name="logo_file" accept="image/*">
                            @error('logo_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Max 2MB. Supports: JPEG, PNG, JPG, GIF, SVG</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Control Section --}}
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cogs"></i> System Control</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="maintenance_mode" value="0">
                                <input type="checkbox" class="custom-control-input" id="maintenance_mode" 
                                       name="maintenance_mode" value="1" 
                                       {{ old('maintenance_mode', $settings->maintenance_mode) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="maintenance_mode">
                                    <strong>Maintenance Mode</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                When enabled, only administrators can access the site.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('admin.settings.toggle-maintenance') }}" 
                                   class="btn btn-{{ $settings->maintenance_mode ? 'success' : 'warning' }}">
                                    <i class="fas fa-{{ $settings->maintenance_mode ? 'play' : 'pause' }}"></i>
                                    {{ $settings->maintenance_mode ? 'Disable' : 'Enable' }} Maintenance
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmReset()">
                                    <i class="fas fa-undo"></i> Reset to Defaults
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Current Settings Info --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-light collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Current Settings Information</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Database Information</h6>
                            <ul class="list-unstyled">
                                <li><strong>Settings ID:</strong> {{ $settings->id ?? 'N/A' }}</li>
                                <li><strong>Last Updated:</strong> {{ $settings->updated_at ?? 'N/A' }}</li>
                                <li><strong>Created:</strong> {{ $settings->created_at ?? 'N/A' }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>System Information</h6>
                            <ul class="list-unstyled">
                                <li><strong>Laravel Version:</strong> {{ app()->version() }}</li>
                                <li><strong>PHP Version:</strong> {{ PHP_VERSION }}</li>
                                <li><strong>Environment:</strong> {{ app()->environment() }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reset Confirmation Form (hidden) --}}
<form id="resetForm" action="{{ route('admin.settings.reset') }}" method="POST" style="display: none;">
    @csrf
</form>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Update color preview on change
    $('#primary_color').on('input', function() {
        $(this).siblings('.input-group-append').find('.input-group-text').text($(this).val());
    });
    
    $('#secondary_color').on('input', function() {
        $(this).siblings('.input-group-append').find('.input-group-text').text($(this).val());
    });
});

function confirmReset() {
    if (confirm('Are you sure you want to reset all settings to defaults? This action cannot be undone.')) {
        document.getElementById('resetForm').submit();
    }
}
</script>
@stop

@section('css')
<style>
.card-header .card-title i {
    margin-right: 8px;
}
.form-group label span.text-danger {
    font-weight: bold;
}
.custom-control-label strong {
    color: #dc3545;
}
.alert ul {
    padding-left: 20px;
}
</style>
@stop
