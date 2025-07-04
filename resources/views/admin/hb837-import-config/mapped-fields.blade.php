@extends('adminlte::page')

@section('title', 'HB837 Mapped Fields')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>HB837 Import - Mapped Fields Overview</h1>
        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Import Config
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Field Mapping Status</h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ count($mappedFieldsData) }} Fields Mapped</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-bordered table-striped table-sm" id="mapped-fields-table">
                        <thead>
                            <tr>
                                <th>Database Field</th>
                                <th>Excel Columns</th>
                                <th>Column Status</th>
                                <th>Column Type</th>
                                <th>Validation</th>
                                <th>Transformation</th>
                                <th>Status Mapping</th>
                                <th>Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mappedFieldsData as $field)
                            <tr>
                                <td>
                                    <strong>{{ $field['database_field'] }}</strong>
                                </td>
                                <td>
                                    @foreach($field['excel_columns'] as $column)
                                        <span class="badge badge-light">{{ $column }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($field['column_exists'])
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Exists
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Missing
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $field['column_type'] }}</code>
                                </td>
                                <td>
                                    @if($field['has_validation'])
                                        <span class="badge badge-info">
                                            <i class="fas fa-check-circle"></i> Yes
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times"></i> No
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($field['has_transformation'])
                                        <span class="badge badge-primary">
                                            <i class="fas fa-exchange-alt"></i> {{ ucfirst($field['has_transformation']) }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times"></i> None
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($field['has_status_map'])
                                        <span class="badge badge-success">
                                            <i class="fas fa-map"></i> Mapped
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times"></i> None
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($field['is_required'])
                                        <span class="badge badge-danger">
                                            <i class="fas fa-asterisk"></i> Required
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times"></i> Optional
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Information Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Fields</span>
                <span class="info-box-number">{{ count($mappedFieldsData) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">DB Columns Exist</span>
                <span class="info-box-number">{{ collect($mappedFieldsData)->where('column_exists', true)->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Missing Columns</span>
                <span class="info-box-number">{{ collect($mappedFieldsData)->where('column_exists', false)->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-exchange-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">With Transformations</span>
                <span class="info-box-number">{{ collect($mappedFieldsData)->where('has_transformation', '!=', false)->count() }}</span>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .badge {
        font-size: 0.75em;
        margin: 1px;
        padding: 0.25em 0.4em;
    }
    
    .table td {
        vertical-align: middle;
        padding: 0.5rem 0.3rem;
        font-size: 0.9em;
    }
    
    .table th {
        padding: 0.5rem 0.3rem;
        font-size: 0.85em;
        font-weight: 600;
    }
    
    .info-box {
        margin-bottom: 20px;
    }
    
    .table-responsive-sm {
        overflow-x: visible;
    }
    
    #mapped-fields-table {
        width: 100% !important;
    }
    
    .dataTables_wrapper {
        overflow-x: visible;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('#mapped-fields-table').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        scrollX: false,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: [1, 4, 5, 6, 7] },
            { width: "15%", targets: 0 },
            { width: "20%", targets: 1 },
            { width: "10%", targets: 2 },
            { width: "10%", targets: 3 },
            { width: "10%", targets: 4 },
            { width: "15%", targets: 5 },
            { width: "10%", targets: 6 },
            { width: "10%", targets: 7 }
        ]
    });
});
</script>
@stop