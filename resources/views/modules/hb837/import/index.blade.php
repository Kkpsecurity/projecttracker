@extends('adminlte::page')

@section('title', 'HB837 - 3-Phase Import')

@section('content_header')
    <x-breadcrumb :items="[
        ['title' => 'HB837 Module', 'url' => route('modules.hb837.index'), 'icon' => 'fas fa-building'],
        ['title' => 'Import', 'icon' => 'fas fa-upload']
    ]" />

    <div class="d-flex justify-content-between align-items-center">
        <h1>HB837 - 3-Phase Import</h1>
        <div class="btn-group">
            <a href="{{ route('modules.hb837.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('modules.hb837.export.template') }}" class="btn btn-outline-primary">
                <i class="fas fa-download"></i> Download Template
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Progress Steps -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="steps-progress">
                        <div class="step-item active" id="step-upload">
                            <div class="step-number">1</div>
                            <div class="step-title">Upload File</div>
                        </div>
                        <div class="step-connector"></div>
                        <div class="step-item" id="step-mapping">
                            <div class="step-number">2</div>
                            <div class="step-title">Map Fields</div>
                        </div>
                        <div class="step-connector"></div>
                        <div class="step-item" id="step-validation">
                            <div class="step-number">3</div>
                            <div class="step-title">Validate & Import</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 1: Upload File -->
    <div class="phase-container" id="phase-upload" style="display: block;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Phase 1: Upload File</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="upload-zone" id="uploadZone">
                            <div class="text-center p-5">
                                <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
                                <h4>Drag & Drop your file here</h4>
                                <p class="text-muted">or <button type="button" class="btn btn-link p-0" onclick="$('#fileInput').click()">browse to choose a file</button></p>
                                <p class="small text-muted">Supported formats: CSV, Excel (XLSX, XLS) - Max 10MB</p>
                            </div>
                            <input type="file" id="fileInput" accept=".csv,.xlsx,.xls" style="display: none;">
                        </div>
                        <div class="upload-progress" id="uploadProgress" style="display: none;">
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <p class="text-center">Uploading and analyzing file...</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-panel">
                            <h5>Import Guidelines</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> CSV or Excel files only</li>
                                <li><i class="fas fa-check text-success"></i> First row should contain headers</li>
                                <li><i class="fas fa-check text-success"></i> Required: Property Name, Address, City, Zip</li>
                                <li><i class="fas fa-check text-success"></i> Maximum file size: 10MB</li>
                            </ul>
                            <hr>
                            <a href="{{ route('modules.hb837.export.template') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 2: Field Mapping -->
    <div class="phase-container" id="phase-mapping" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Phase 2: Map Fields</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="mappingContainer">
                            <!-- Field mapping interface will be populated here -->
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary" onclick="goToPhase('upload')">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="generatePreview()">
                                <i class="fas fa-eye"></i> Preview Data
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-panel">
                            <h5>Field Mapping</h5>
                            <p class="text-muted">Map your file columns to database fields. Required fields are marked with <span class="text-danger">*</span></p>
                            <div id="mappingStats"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 3: Validation & Import -->
    <div class="phase-container" id="phase-validation" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Phase 3: Validate & Import</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Preview Data -->
                        <div id="previewContainer">
                            <!-- Data preview will be shown here -->
                        </div>

                        <!-- Import Options -->
                        <div class="import-options mt-4">
                            <h5>Import Options</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="truncateMode">
                                <label class="form-check-label" for="truncateMode">
                                    Replace all existing data (truncate mode)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="updateMode" checked>
                                <label class="form-check-label" for="updateMode">
                                    Update existing records (match by Property Name + Address)
                                </label>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary" onclick="goToPhase('mapping')">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="button" class="btn btn-success" onclick="executeImport()" id="importBtn">
                                <i class="fas fa-database"></i> Execute Import
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-panel">
                            <h5>Validation Summary</h5>
                            <div id="validationSummary">
                                <!-- Validation results will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Complete -->
    <div class="phase-container" id="phase-complete" style="display: none;">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title text-white">Import Complete</h3>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>Import Completed Successfully!</h4>
                    <div id="importResults" class="mt-4">
                        <!-- Import results will be shown here -->
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('modules.hb837.index') }}" class="btn btn-primary">
                            <i class="fas fa-dashboard"></i> Go to Dashboard
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="startNewImport()">
                            <i class="fas fa-plus"></i> Start New Import
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
.steps-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px 0;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step-item.active .step-number {
    background-color: #007bff;
    color: white;
}

.step-item.completed .step-number {
    background-color: #28a745;
    color: white;
}

.step-connector {
    width: 100px;
    height: 2px;
    background-color: #e9ecef;
    margin: 0 20px;
    margin-bottom: 32px;
}

.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.upload-zone.dragover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.info-panel {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.field-mapping-row {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #e9ecef;
}

.field-mapping-row:last-child {
    border-bottom: none;
}

.required-field {
    color: #dc3545;
}
</style>
@stop

@section('js')
<script>
let currentPhase = 'upload';
let fileInfo = null;
let mappings = {};
let sessionId = null;

$(document).ready(function() {
    initializeUpload();
});

function initializeUpload() {
    // File input change
    $('#fileInput').on('change', function() {
        if (this.files.length > 0) {
            uploadFile(this.files[0]);
        }
    });

    // Drag and drop
    const uploadZone = document.getElementById('uploadZone');

    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            uploadFile(files[0]);
        }
    });
}

function uploadFile(file) {
    const formData = new FormData();
    formData.append('file', file);

    $('#uploadZone').hide();
    $('#uploadProgress').show();

    $.ajax({
        url: '{{ route("modules.hb837.import.upload") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        xhr: function() {
            const xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    $('.progress-bar').css('width', percentComplete + '%');
                }
            }, false);
            return xhr;
        },
        success: function(response) {
            if (response.success) {
                fileInfo = response.file_info;
                sessionId = response.file_info.upload_session;
                setupFieldMapping(response);
                goToPhase('mapping');
            } else {
                toastr.error('Upload failed: ' + response.error);
                resetUpload();
            }
        },
        error: function() {
            toastr.error('Upload failed. Please try again.');
            resetUpload();
        }
    });
}

function setupFieldMapping(data) {
    const container = $('#mappingContainer');
    const availableFields = data.available_fields;
    const suggestedMappings = data.suggested_mappings;
    const requiredFields = data.required_fields;
    const headers = data.structure.headers || [];

    let html = '<div class="field-mappings">';
    html += '<h5>Map File Columns to Database Fields</h5>';
    html += '<div class="table-responsive"><table class="table table-bordered">';
    html += '<thead><tr><th>File Column</th><th>Maps To</th><th>Required</th><th>Sample Data</th></tr></thead><tbody>';

    headers.forEach((header, index) => {
        const suggested = suggestedMappings[header] || '';
        const sampleData = data.structure.sample_data && data.structure.sample_data[0]
            ? data.structure.sample_data[0][header] || '' : '';

        html += '<tr>';
        html += `<td><strong>${header}</strong></td>`;
        html += '<td><select name="mapping[' + header + ']" class="form-control mapping-select">';
        html += '<option value="">-- Select Field --</option>';

        Object.entries(availableFields).forEach(([field, description]) => {
            const selected = suggested === field ? 'selected' : '';
            const required = requiredFields.includes(field) ? ' *' : '';
            html += `<option value="${field}" ${selected}>${description}${required}</option>`;
        });

        html += '</select></td>';
        html += '<td>' + (requiredFields.includes(suggested) ? '<span class="text-danger">*</span>' : '') + '</td>';
        html += '<td><small class="text-muted">' + sampleData + '</small></td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    html += '</div>';

    container.html(html);

    // Update mappings object when selections change
    $('.mapping-select').on('change', function() {
        updateMappings();
    });

    updateMappings();
}

function updateMappings() {
    mappings = {};
    $('.mapping-select').each(function() {
        const header = $(this).attr('name').match(/\[(.*?)\]/)[1];
        const value = $(this).val();
        if (value) {
            mappings[header] = value;
        }
    });

    updateMappingStats();
}

function updateMappingStats() {
    const requiredFields = ['property_name', 'address', 'city', 'zip']; // From config
    const mappedRequired = requiredFields.filter(field => Object.values(mappings).includes(field));

    const stats = `
        <div class="alert alert-info">
            <strong>Mapping Status:</strong><br>
            Total fields mapped: ${Object.keys(mappings).length}<br>
            Required fields mapped: ${mappedRequired.length}/${requiredFields.length}
        </div>
    `;

    $('#mappingStats').html(stats);
}

function generatePreview() {
    if (Object.keys(mappings).length === 0) {
        toastr.warning('Please map at least one field before previewing.');
        return;
    }

    $.ajax({
        url: '{{ route("modules.hb837.import.map-fields") }}',
        method: 'POST',
        data: {
            file_path: fileInfo.stored_path,
            mappings: mappings,
            session_id: sessionId
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                setupValidationPhase(response);
                goToPhase('validation');
            } else {
                toastr.error('Preview failed: ' + response.error);
            }
        },
        error: function() {
            toastr.error('Preview request failed');
        }
    });
}

function setupValidationPhase(data) {
    // Show preview data
    const previewData = data.preview.preview_data || [];
    const validation = data.validation;

    let html = '<h5>Data Preview (First 10 rows)</h5>';

    if (previewData.length > 0) {
        html += '<div class="table-responsive"><table class="table table-sm table-striped">';
        html += '<thead><tr>';
        Object.keys(previewData[0]).forEach(field => {
            html += `<th>${field}</th>`;
        });
        html += '</tr></thead><tbody>';

        previewData.forEach(row => {
            html += '<tr>';
            Object.values(row).forEach(value => {
                html += `<td>${value || '<em>empty</em>'}</td>`;
            });
            html += '</tr>';
        });

        html += '</tbody></table></div>';
    }

    $('#previewContainer').html(html);

    // Show validation summary
    const validationHtml = `
        <div class="alert ${validation.is_valid ? 'alert-success' : 'alert-warning'}">
            <strong>Validation Results:</strong><br>
            Total rows: ${validation.total_rows || 0}<br>
            Valid rows: ${validation.valid_rows || 0}<br>
            Invalid rows: ${validation.invalid_rows || 0}<br>
            ${validation.is_valid ? 'Ready to import!' : 'Please fix validation errors before importing.'}
        </div>
        ${validation.validation_errors && validation.validation_errors.length > 0 ?
            '<div class="alert alert-danger"><strong>Validation Errors:</strong><ul>' +
            validation.validation_errors.map(error => `<li>Row ${error.row}: ${error.errors.join(', ')}</li>`).join('') +
            '</ul></div>' : ''}
    `;

    $('#validationSummary').html(validationHtml);

    // Enable/disable import button based on validation
    $('#importBtn').prop('disabled', !validation.is_valid);
}

function executeImport() {
    if (confirm('Are you sure you want to execute the import? This action cannot be undone.')) {
        const options = {
            truncate: $('#truncateMode').is(':checked'),
            update_existing: $('#updateMode').is(':checked')
        };

        $('#importBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importing...');

        $.ajax({
            url: '{{ route("modules.hb837.import.execute") }}',
            method: 'POST',
            data: {
                file_path: fileInfo.stored_path,
                mappings: mappings,
                options: options
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showImportResults(response.results);
                    goToPhase('complete');
                } else {
                    toastr.error('Import failed: ' + response.error);
                    $('#importBtn').prop('disabled', false).html('<i class="fas fa-database"></i> Execute Import');
                }
            },
            error: function() {
                toastr.error('Import request failed');
                $('#importBtn').prop('disabled', false).html('<i class="fas fa-database"></i> Execute Import');
            }
        });
    }
}

function showImportResults(results) {
    const html = `
        <div class="row">
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-plus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Imported</span>
                        <span class="info-box-number">${results.imported_count || 0}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-edit"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Updated</span>
                        <span class="info-box-number">${results.updated_count || 0}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-skip-forward"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Skipped</span>
                        <span class="info-box-number">${results.skipped_count || 0}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total</span>
                        <span class="info-box-number">${results.total_processed || 0}</span>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#importResults').html(html);
}

function goToPhase(phase) {
    // Hide all phases
    $('.phase-container').hide();

    // Show target phase
    $('#phase-' + phase).show();

    // Update progress steps
    $('.step-item').removeClass('active completed');

    if (phase === 'upload') {
        $('#step-upload').addClass('active');
    } else if (phase === 'mapping') {
        $('#step-upload').addClass('completed');
        $('#step-mapping').addClass('active');
    } else if (phase === 'validation') {
        $('#step-upload, #step-mapping').addClass('completed');
        $('#step-validation').addClass('active');
    } else if (phase === 'complete') {
        $('#step-upload, #step-mapping, #step-validation').addClass('completed');
    }

    currentPhase = phase;
}

function resetUpload() {
    $('#uploadProgress').hide();
    $('#uploadZone').show();
    $('.progress-bar').css('width', '0%');
}

function startNewImport() {
    location.reload();
}
</script>
@stop
