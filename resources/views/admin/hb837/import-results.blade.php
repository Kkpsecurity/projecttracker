@extends('adminlte::page')

@section('title', 'Import Results - HB837 Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-check-circle text-success"></i> Import Results</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.smart-import.show') }}">Smart Import</a></li>
                <li class="breadcrumb-item active">Import Results</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Import Success Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i> Import Completed Successfully
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">{{ $results['import_timestamp']->format('M d, Y g:i A') }}</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Import Statistics -->
                        <div class="col-md-8">
                            <h5><i class="fas fa-chart-bar"></i> Import Statistics</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-plus"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">New Records</span>
                                            <span class="info-box-number">{{ number_format($results['imported']) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-edit"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Updated</span>
                                            <span class="info-box-number">{{ number_format($results['updated']) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-skip-forward"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Skipped</span>
                                            <span class="info-box-number">{{ number_format($results['skipped']) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-secondary">
                                        <span class="info-box-icon"><i class="fas fa-file"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total</span>
                                            <span class="info-box-number">{{ number_format($results['total_processed']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="progress-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h6>Import Progress</h6>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <span class="text-success">{{ $results['total_processed'] }} records processed</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    @php
                                        $successRate = $results['total_processed'] > 0 ? (($results['imported'] + $results['updated']) / $results['total_processed']) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $successRate }}%">
                                        {{ number_format($successRate, 1) }}% Success
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-md-4">
                            <h5><i class="fas fa-tools"></i> Quick Actions</h5>
                            <div class="btn-group-vertical btn-block">
                                <a href="{{ route('admin.hb837.index') }}" class="btn btn-primary">
                                    <i class="fas fa-list"></i> View All Records
                                </a>
                                <a href="{{ route('admin.hb837.index', ['tab' => 'active']) }}" class="btn btn-success">
                                    <i class="fas fa-eye"></i> View Active Projects
                                </a>
                                <a href="{{ route('admin.hb837.smart-import.show') }}" class="btn btn-info">
                                    <i class="fas fa-upload"></i> Import Another File
                                </a>
                                <a href="{{ route('admin.hb837.export') }}" class="btn btn-warning">
                                    <i class="fas fa-download"></i> Export Data
                                </a>
                            </div>

                            <!-- File Information -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title">File Information</h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">File:</dt>
                                        <dd class="col-sm-8">{{ $results['file_name'] }}</dd>
                                        
                                        <dt class="col-sm-4">Type:</dt>
                                        <dd class="col-sm-8">{{ $results['analysis']['detected_type'] ?? 'HB837 Data' }}</dd>
                                        
                                        <dt class="col-sm-4">Columns:</dt>
                                        <dd class="col-sm-8">{{ $results['analysis']['stats']['columns'] ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Errors Section (if any) -->
    @if(!empty($results['errors']))
    <div class="row">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Import Warnings & Errors
                    </h3>
                    <div class="card-tools">
                        @if(is_array($results['errors']) && !empty($results['errors']))
                            <span class="badge badge-warning">{{ count($results['errors']) }} issues</span>
                        @elseif(is_string($results['errors']) && !empty($results['errors']))
                            <span class="badge badge-warning">Issues found</span>
                        @else
                            <span class="badge badge-warning">Issues found</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(is_array($results['errors']) && !empty($results['errors']))
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Field</th>
                                        <th>Issue</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['errors'] as $errorIndex => $error)
                                    <tr>
                                        @if(is_array($error) && isset($error['row']))
                                            {{-- Structured error with row, field, message, value --}}
                                            <td>{{ $error['row'] ?? 'N/A' }}</td>
                                            <td><code>{{ $error['field'] ?? 'General' }}</code></td>
                                            <td>{{ $error['message'] ?? $error['error'] ?? 'Unknown error' }}</td>
                                            <td>
                                                @if(isset($error['value']))
                                                    <code>{{ Str::limit($error['value'], 50) }}</code>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @elseif(is_array($error))
                                            {{-- Array error but no row info --}}
                                            <td>{{ $errorIndex + 1 }}</td>
                                            <td><code>{{ $error['field'] ?? 'General' }}</code></td>
                                            <td>{{ $error['message'] ?? $error['error'] ?? implode(', ', $error) }}</td>
                                            <td>{{ isset($error['value']) ? Str::limit($error['value'], 50) : '-' }}</td>
                                        @else
                                            {{-- Simple string error --}}
                                            <td>{{ $errorIndex + 1 }}</td>
                                            <td><code>General</code></td>
                                            <td>{{ is_string($error) ? $error : 'Unknown error format' }}</td>
                                            <td>-</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(is_string($results['errors']) && !empty($results['errors']))
                        {{-- Handle string errors --}}
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Import Issues:</h6>
                            <p>{{ $results['errors'] }}</p>
                        </div>
                    @else
                        {{-- Handle other error formats --}}
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Import Issues:</h6>
                            <p>Some issues occurred during import. Please check the import logs for details.</p>
                            @if(is_object($results['errors']) || is_array($results['errors']))
                                <details>
                                    <summary>Raw Error Data</summary>
                                    <pre>{{ print_r($results['errors'], true) }}</pre>
                                </details>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Column Mapping Summary -->
    @if(isset($results['analysis']['mapping']))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt"></i> Column Mapping Applied
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($results['analysis']['mapping'] as $mapping)
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="card card-outline card-secondary">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Excel:</small><br>
                                            <strong>{{ $mapping['source_column'] ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="text-center">
                                            <i class="fas fa-arrow-right text-muted"></i>
                                        </div>
                                        <div class="text-right">
                                            <small class="text-muted">Database:</small><br>
                                            <code>{{ $mapping['target_field'] ?? 'unmapped' }}</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@stop

@section('css')
<style>
.import-success-animation {
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.info-box {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.progress {
    height: 25px;
    border-radius: 12px;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Add animation to the main card
    $('.card-success').addClass('import-success-animation');
    
    // Auto-refresh button states
    setTimeout(function() {
        $('.btn').removeClass('disabled');
    }, 1000);
});
</script>
@stop
