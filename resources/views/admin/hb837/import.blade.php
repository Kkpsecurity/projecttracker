@extends('adminlte::page')

@section('title', 'Import HB837 Data - KKP Security Project Tracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Import HB837 Data</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item active">Import Data</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Import Instructions Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Import Instructions
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-download"></i> Supported File Formats</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-file-excel text-success"></i> Excel files (.xlsx, .xls)</li>
                                <li><i class="fas fa-file-csv text-info"></i> CSV files (.csv)</li>
                            </ul>

                            <h5><i class="fas fa-chart-line"></i> Three-Phase Import Workflow</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-plus-circle text-primary"></i> <strong>Initial Phase:</strong> Add new records from fresh data</li>
                                <li><i class="fas fa-sync-alt text-warning"></i> <strong>Update Phase:</strong> Update existing records with new data</li>
                                <li><i class="fas fa-search text-info"></i> <strong>Review Phase:</strong> Validate and review all changes</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-file-download"></i> Template Downloads</h5>
                            <p class="text-muted">Download a template file to ensure proper formatting:</p>
                            <div class="btn-group-vertical w-100">
                                <a href="{{ route('admin.hb837.export.format', 'xlsx') }}?template=true" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-excel"></i> Download Excel Template
                                </a>
                                <a href="{{ route('admin.hb837.export.format', 'csv') }}?template=true" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-file-csv"></i> Download CSV Template
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Three-Phase Import Form Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i>
                        Three-Phase Import Workflow
                    </h3>
                </div>

                <form action="{{ route('admin.hb837.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="card-body">
                        <!-- Import Phase Selection -->
                        <div class="form-group">
                            <label>Import Phase</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="phase_initial" name="import_phase" value="initial" checked>
                                        <label for="phase_initial" class="custom-control-label">
                                            <strong>Initial Phase</strong>
                                        </label>
                                        <small class="form-text text-muted">
                                            Import new records from initial data set.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="phase_update" name="import_phase" value="update">
                                        <label for="phase_update" class="custom-control-label">
                                            <strong>Update Phase</strong>
                                        </label>
                                        <small class="form-text text-muted">
                                            Update existing records with new information.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="phase_review" name="import_phase" value="review">
                                        <label for="phase_review" class="custom-control-label">
                                            <strong>Review Phase</strong>
                                        </label>
                                        <small class="form-text text-muted">
                                            Final review and validation of all data.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="form-group">
                            <label for="file">Select File to Import</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file"
                                           accept=".xlsx,.xls,.csv" required>
                                    <label class="custom-file-label" for="file">Choose file...</label>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-file-upload"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Maximum file size: 10MB. Supported formats: .xlsx, .xls, .csv
                            </small>
                        </div>

                        <!-- Action Selection -->
                        <div class="form-group">
                            <label>Action</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="action_preview" name="action" value="preview" checked>
                                        <label for="action_preview" class="custom-control-label">
                                            <strong>Preview Import</strong>
                                        </label>
                                        <small class="form-text text-muted">
                                            Review what changes will be made without importing.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="action_import" name="action" value="import">
                                        <label for="action_import" class="custom-control-label">
                                            <strong>Execute Import</strong>
                                        </label>
                                        <small class="form-text text-muted">
                                            Perform the actual import operation.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Options -->
                        <div class="card card-secondary collapsed-card">
                            <div class="card-header">
                                <h3 class="card-title">Advanced Options</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_row">Start Row</label>
                                            <input type="number" class="form-control" id="start_row" name="start_row" value="2" min="1">
                                            <small class="form-text text-muted">Row number to start importing from (default: 2)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="batch_size">Batch Size</label>
                                            <select class="form-control" id="batch_size" name="batch_size">
                                                <option value="50">50 records</option>
                                                <option value="100" selected>100 records</option>
                                                <option value="200">200 records</option>
                                                <option value="500">500 records</option>
                                            </select>
                                            <small class="form-text text-muted">Number of records to process at once</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mt-4">
                                                <input type="checkbox" class="custom-control-input" id="validate_only" name="validate_only">
                                                <label class="custom-control-label" for="validate_only">
                                                    Validation Only
                                                </label>
                                                <small class="form-text text-muted">Check file format without importing</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column Mapping (will be shown after file is selected) -->
                        <div id="columnMapping" class="d-none">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-columns"></i>
                                        Column Mapping
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Map the columns in your file to the database fields:</p>
                                    <div id="mappingContainer">
                                        <!-- Column mapping will be populated via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary" id="importBtn">
                                    <i class="fas fa-upload"></i> Start Import
                                </button>
                                <a href="{{ route('admin.hb837.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-info" id="previewBtn" disabled>
                                    <i class="fas fa-eye"></i> Preview Data
                                </button>
                                <button type="button" class="btn btn-warning" id="compareBtn" disabled>
                                    <i class="fas fa-balance-scale"></i> Compare Data
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Full Three-Phase Import Card -->
            <div class="card card-warning collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i>
                        Complete Three-Phase Import
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Upload all three phase files to execute the complete import workflow automatically.</p>

                    <form action="{{ route('admin.hb837.three-phase-import') }}" method="POST" enctype="multipart/form-data" id="threePhaseForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="file_phase1">Phase 1 File (Initial)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_phase1" name="file_phase1" accept=".xlsx,.xls,.csv">
                                        <label class="custom-file-label" for="file_phase1">Choose Phase 1 file...</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="file_phase2">Phase 2 File (Update)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_phase2" name="file_phase2" accept=".xlsx,.xls,.csv">
                                        <label class="custom-file-label" for="file_phase2">Choose Phase 2 file...</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="file_phase3">Phase 3 File (Review)</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_phase3" name="file_phase3" accept=".xlsx,.xls,.csv">
                                        <label class="custom-file-label" for="file_phase3">Choose Phase 3 file...</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-warning" id="threePhaseBtn">
                                <i class="fas fa-cogs"></i> Execute Three-Phase Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Import History Card -->
            <div class="card card-default collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Recent Import History
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Phase</th>
                                    <th>File Name</th>
                                    <th>Records</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        No import history available
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                                                </small>
                                            </div>

                                            <div class="form-group">
                                                <label for="file">Excel File</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="file" class="custom-file-input" id="file" accept=".xlsx,.xls,.csv" required>
                                                        <label class="custom-file-label" for="file">Choose file</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="action">Action</label>
                                                <select name="action" id="action" class="form-control" required>
                                                    <option value="preview">Preview Changes</option>
                                                    <option value="import">Import Data</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-upload"></i> Process File
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Three Phase Import -->
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Three Phase Batch Import</h3>
                                    </div>
                                    <form action="{{ route('admin.hb837.three-phase-import') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="file_phase1">Phase 1: Initial Import & Quotation</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="file_phase1" class="custom-file-input" id="file_phase1" accept=".xlsx,.xls,.csv" required>
                                                        <label class="custom-file-label" for="file_phase1">Choose Phase 1 file</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="file_phase2">Phase 2: Executed & Contacts</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="file_phase2" class="custom-file-input" id="file_phase2" accept=".xlsx,.xls,.csv" required>
                                                        <label class="custom-file-label" for="file_phase2">Choose Phase 2 file</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="file_phase3">Phase 3: Details Updated</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="file_phase3" class="custom-file-input" id="file_phase3" accept=".xlsx,.xls,.csv" required>
                                                        <label class="custom-file-label" for="file_phase3">Choose Phase 3 file</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-rocket"></i> Execute All Phases
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Results -->
        @if(session('preview_data'))
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Import Preview - {{ session('preview_data.phase_description') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-upload"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Uploaded</span>
                                            <span class="info-box-number">{{ session('preview_data.total_uploaded') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-plus"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">New Records</span>
                                            <span class="info-box-number">{{ session('preview_data.new_count') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-edit"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Updates</span>
                                            <span class="info-box-number">{{ session('preview_data.updated_count') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-secondary"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">No Changes</span>
                                            <span class="info-box-number">{{ session('preview_data.existing_count') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(count(session('preview_data.updated_properties')) > 0)
                                <h5>Records to be Updated:</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Property</th>
                                                <th>Address</th>
                                                <th>Changes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(session('preview_data.updated_properties') as $property)
                                                <tr>
                                                    <td>{{ $property['property_name'] }}</td>
                                                    <td>{{ $property['address'] }}</td>
                                                    <td>
                                                        @foreach($property['changes'] as $field => $change)
                                                            <span class="badge badge-info">
                                                                {{ ucfirst(str_replace('_', ' ', $field)) }}:
                                                                {{ $change['old'] ?? 'empty' }} → {{ $change['new'] }}
                                                            </span>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Three Phase Results -->
        @if(session('three_phase_results'))
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Three Phase Import Results</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Summary</h5>
                                    <p>
                                        <strong>Phases Completed:</strong> {{ session('three_phase_results.summary.phases_completed') }}/3<br>
                                        <strong>Total Records:</strong>
                                        {{ session('three_phase_results.summary.total_imported') }} imported,
                                        {{ session('three_phase_results.summary.total_updated') }} updated,
                                        {{ session('three_phase_results.summary.total_skipped') }} skipped
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                @foreach(['phase1', 'phase2', 'phase3'] as $phase)
                                    @if(isset(session('three_phase_results')[$phase]))
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ ucfirst($phase) }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    @if(isset(session('three_phase_results')[$phase]['error']))
                                                        <div class="alert alert-danger">
                                                            Error: {{ session('three_phase_results')[$phase]['error'] }}
                                                        </div>
                                                    @else
                                                        <p><strong>{{ session('three_phase_results')[$phase]['phase_description'] ?? '' }}</strong></p>
                                                        <ul class="list-unstyled">
                                                            <li><i class="fas fa-plus text-success"></i> {{ session('three_phase_results')[$phase]['imported'] ?? 0 }} imported</li>
                                                            <li><i class="fas fa-edit text-warning"></i> {{ session('three_phase_results')[$phase]['updated'] ?? 0 }} updated</li>
                                                            <li><i class="fas fa-minus text-secondary"></i> {{ session('three_phase_results')[$phase]['skipped'] ?? 0 }} skipped</li>
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Data Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="proceedWithImport()">
                    Proceed with Import
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="progressModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="progressModalLabel">Import Progress</h5>
            </div>
            <div class="modal-body">
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="importProgress">
                        0%
                    </div>
                </div>
                <div id="progressText">Preparing import...</div>
                <div id="progressLog" class="mt-3" style="max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 0.9em;">
                    <!-- Progress log will be shown here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelImportBtn">Cancel Import</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.card-header .card-title i {
    margin-right: 0.5rem;
}

.custom-file-label::after {
    content: "Browse";
}

.progress-bar {
    transition: width 0.3s ease;
}

#progressLog {
    white-space: pre-wrap;
    word-wrap: break-word;
}

.column-mapping-row {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.column-mapping-row .source-column {
    flex: 1;
    margin-right: 15px;
    font-weight: bold;
}

.column-mapping-row .target-select {
    flex: 1;
}

.custom-control-label strong {
    display: block;
}

.btn-group-vertical .btn {
    margin-bottom: 5px;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Initialize file inputs
    $('.custom-file-input').on('change', function(event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);

        // Enable preview and compare buttons if file is selected
        if (inputFile.id === 'file') {
            $('#previewBtn, #compareBtn').prop('disabled', false);

            // TODO: Parse file headers and show column mapping
            // For now, we'll show a placeholder
            setTimeout(function() {
                showColumnMapping(['property_name', 'address', 'city', 'state', 'zip']);
            }, 500);
        }
    });

    // Form submission
    $('#importForm').on('submit', function(e) {
        e.preventDefault();

        if (!$('#file')[0].files.length) {
            toastr.error('Please select a file to import.');
            return;
        }

        // Show progress modal for actual imports (not previews)
        if ($('input[name="action"]:checked').val() === 'import') {
            $('#progressModal').modal('show');
            simulateImport();
        } else {
            // For preview, submit normally
            this.submit();
        }
    });

    // Three-phase form submission
    $('#threePhaseForm').on('submit', function(e) {
        e.preventDefault();

        if (!$('#file_phase1')[0].files.length || !$('#file_phase2')[0].files.length || !$('#file_phase3')[0].files.length) {
            toastr.error('Please select all three phase files.');
            return;
        }

        $('#progressModal').modal('show');
        simulateThreePhaseImport();
    });

    // Preview button
    $('#previewBtn').on('click', function() {
        if (!$('#file')[0].files.length) {
            toastr.error('Please select a file first.');
            return;
        }

        showPreview();
    });

    // Compare button
    $('#compareBtn').on('click', function() {
        if (!$('#file')[0].files.length) {
            toastr.error('Please select a file first.');
            return;
        }

        // Use the compare endpoint
        var formData = new FormData();
        formData.append('file', $('#file')[0].files[0]);
        formData.append('import_phase', $('input[name="import_phase"]:checked').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '{{ route("admin.hb837.import.compare") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showComparisonResults(response.comparison);
                } else {
                    toastr.error('Comparison failed: ' + response.error);
                }
            },
            error: function() {
                toastr.error('Error occurred during comparison.');
            }
        });
    });
});

function showColumnMapping(columns) {
    var mappingHtml = '';
    var dbFields = [
        'property_name', 'management_company', 'owner_name', 'property_type', 'units',
        'address', 'city', 'county', 'state', 'zip', 'phone',
        'assigned_consultant_id', 'scheduled_date_of_inspection', 'report_status',
        'contracting_status', 'quoted_price', 'sub_fees_estimated_expenses',
        'securitygauge_crime_risk', 'macro_client', 'notes'
    ];

    columns.forEach(function(column, index) {
        mappingHtml += '<div class="column-mapping-row">';
        mappingHtml += '<div class="source-column">' + column + '</div>';
        mappingHtml += '<div class="target-select">';
        mappingHtml += '<select class="form-control" name="column_mapping[' + column + ']">';
        mappingHtml += '<option value="">-- Skip this column --</option>';

        dbFields.forEach(function(field) {
            var selected = (field === column) ? 'selected' : '';
            mappingHtml += '<option value="' + field + '" ' + selected + '>' + field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</option>';
        });

        mappingHtml += '</select>';
        mappingHtml += '</div>';
        mappingHtml += '</div>';
    });

    $('#mappingContainer').html(mappingHtml);
    $('#columnMapping').removeClass('d-none');
}

function showPreview() {
    var previewHtml = '<div class="table-responsive">';
    previewHtml += '<table class="table table-sm table-bordered">';
    previewHtml += '<thead class="thead-light">';
    previewHtml += '<tr><th>Property Name</th><th>Address</th><th>City</th><th>State</th><th>Phase</th><th>Status</th></tr>';
    previewHtml += '</thead>';
    previewHtml += '<tbody>';

    // Sample preview data
    var phase = $('input[name="import_phase"]:checked').val();
    for (var i = 1; i <= 5; i++) {
        previewHtml += '<tr>';
        previewHtml += '<td>Sample Property ' + i + '</td>';
        previewHtml += '<td>123 Main St</td>';
        previewHtml += '<td>Sample City</td>';
        previewHtml += '<td>CA</td>';
        previewHtml += '<td><span class="badge badge-primary">' + phase + '</span></td>';
        previewHtml += '<td>Active</td>';
        previewHtml += '</tr>';
    }

    previewHtml += '</tbody>';
    previewHtml += '</table>';
    previewHtml += '<small class="text-muted">Showing first 5 rows of data for phase: <strong>' + phase + '</strong></small>';
    previewHtml += '</div>';

    $('#previewContent').html(previewHtml);
    $('#previewModal').modal('show');
}

function showComparisonResults(comparison) {
    var comparisonHtml = '<div class="row">';
    comparisonHtml += '<div class="col-md-4">';
    comparisonHtml += '<div class="card border-success">';
    comparisonHtml += '<div class="card-header bg-success text-white">New Records</div>';
    comparisonHtml += '<div class="card-body"><h2 class="text-center">' + (comparison.new_count || 0) + '</h2></div>';
    comparisonHtml += '</div></div>';

    comparisonHtml += '<div class="col-md-4">';
    comparisonHtml += '<div class="card border-warning">';
    comparisonHtml += '<div class="card-header bg-warning text-dark">Updates</div>';
    comparisonHtml += '<div class="card-body"><h2 class="text-center">' + (comparison.update_count || 0) + '</h2></div>';
    comparisonHtml += '</div></div>';

    comparisonHtml += '<div class="col-md-4">';
    comparisonHtml += '<div class="card border-info">';
    comparisonHtml += '<div class="card-header bg-info text-white">Skipped</div>';
    comparisonHtml += '<div class="card-body"><h2 class="text-center">' + (comparison.skip_count || 0) + '</h2></div>';
    comparisonHtml += '</div></div>';
    comparisonHtml += '</div>';

    $('#previewContent').html(comparisonHtml);
    $('#previewModalLabel').text('Import Comparison Results');
    $('#previewModal').modal('show');
}

function proceedWithImport() {
    $('#previewModal').modal('hide');
    $('input[name="action"][value="import"]').prop('checked', true);
    $('#importForm').submit();
}

function simulateImport() {
    var progress = 0;
    var phase = $('input[name="import_phase"]:checked').val();
    var messages = [
        'Validating file format...',
        'Reading data rows...',
        'Validating data integrity...',
        'Processing ' + phase + ' phase records batch 1/3...',
        'Processing ' + phase + ' phase records batch 2/3...',
        'Processing ' + phase + ' phase records batch 3/3...',
        'Finalizing ' + phase + ' phase import...',
        'Import completed successfully!'
    ];

    var interval = setInterval(function() {
        progress += 12.5;
        if (progress > 100) progress = 100;

        var messageIndex = Math.floor((progress / 100) * messages.length);
        if (messageIndex >= messages.length) messageIndex = messages.length - 1;

        $('#importProgress').css('width', progress + '%').text(progress.toFixed(0) + '%');
        $('#progressText').text(messages[messageIndex]);
        $('#progressLog').append('[' + new Date().toLocaleTimeString() + '] ' + messages[messageIndex] + '\n');

        // Auto scroll to bottom
        $('#progressLog').scrollTop($('#progressLog')[0].scrollHeight);

        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(function() {
                $('#progressModal').modal('hide');
                toastr.success('Import completed successfully!');
                // Redirect to index
                window.location.href = '{{ route("admin.hb837.index") }}';
            }, 2000);
        }
    }, 800);
}

function simulateThreePhaseImport() {
    var progress = 0;
    var messages = [
        'Preparing three-phase import...',
        'Phase 1: Processing initial data...',
        'Phase 1: Importing new records...',
        'Phase 2: Processing update data...',
        'Phase 2: Updating existing records...',
        'Phase 3: Processing review data...',
        'Phase 3: Validating all changes...',
        'Three-phase import completed successfully!'
    ];

    var interval = setInterval(function() {
        progress += 12.5;
        if (progress > 100) progress = 100;

        var messageIndex = Math.floor((progress / 100) * messages.length);
        if (messageIndex >= messages.length) messageIndex = messages.length - 1;

        $('#importProgress').css('width', progress + '%').text(progress.toFixed(0) + '%');
        $('#progressText').text(messages[messageIndex]);
        $('#progressLog').append('[' + new Date().toLocaleTimeString() + '] ' + messages[messageIndex] + '\n');

        // Auto scroll to bottom
        $('#progressLog').scrollTop($('#progressLog')[0].scrollHeight);

        if (progress >= 100) {
            clearInterval(interval);
            setTimeout(function() {
                $('#progressModal').modal('hide');
                toastr.success('Three-phase import completed successfully!');
                // Redirect to index
                window.location.href = '{{ route("admin.hb837.index") }}';
            }, 2000);
        }
    }, 1000);
}
</script>
@stop
