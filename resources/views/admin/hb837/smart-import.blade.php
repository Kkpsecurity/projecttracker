@extends('adminlte::page')

@section('title', 'Smart Import - HB837 Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-upload"></i> Smart Import</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item active">Smart Import</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Smart Import Card -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-magic"></i> Smart File Import
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#helpModal" title="Help & User Guide">
                            <i class="fas fa-question-circle"></i>
                        </button>
                        <span class="badge badge-light">
                            <i class="fas fa-brain"></i> AI-Powered Detection
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Upload Section -->
                        <div class="col-md-8">
                            <div class="upload-zone" id="upload-zone">
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <h4>Drop your file here or click to browse</h4>
                                    <p class="text-muted">
                                        <strong>Just upload your file!</strong> The system will automatically detect what you're importing and handle everything for you.
                                    </p>
                                    <p class="text-muted small">
                                        <strong>Supported:</strong> Excel (.xlsx, .xls), CSV (.csv), Email attachments (.eml, .msg)
                                    </p>
                                    <input type="file" id="file-input" name="file" accept=".xlsx,.xls,.csv,.eml,.msg" style="display: none;">
                                    <button type="button" class="btn btn-primary btn-lg" onclick="document.getElementById('file-input').click()">
                                        <i class="fas fa-file-plus"></i> Choose File
                                    </button>

                                    <!-- Quick Examples -->
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <strong>Examples:</strong> Property lists, inspection reports, client data files, forwarded emails with attachments
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Section (Hidden initially) -->
                            <div id="progress-section" style="display: none;">
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span id="progress-text">Analyzing file...</span>
                                        <span id="progress-percentage">0%</span>
                                    </div>
                                    <div class="progress">
                                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                             role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Panel -->
                        <div class="col-md-4">
                            <div class="info-panel">
                                <h5><i class="fas fa-magic text-primary"></i> Intelligent Import</h5>
                                <p class="text-muted small">Our smart system automatically handles:</p>

                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-brain text-primary"></i>
                                        <strong>File Detection:</strong> Recognizes data format automatically
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-link text-warning"></i>
                                        <strong>Column Matching:</strong> Maps your columns to our fields
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-shield-alt text-success"></i>
                                        <strong>Data Cleaning:</strong> Fixes common formatting issues
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-search-plus text-info"></i>
                                        <strong>Duplicate Detection:</strong> Finds and handles duplicates
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-eye text-secondary"></i>
                                        <strong>Preview Mode:</strong> Shows changes before importing
                                    </li>
                                </ul>

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Pro Tip:</strong> You can upload client emails with attachments,
                                    and we'll extract the data automatically!
                                </div>

                                <div class="mt-4">
                                    <h6><i class="fas fa-download"></i> Need a Template?</h6>
                                    <div class="btn-group-vertical w-100">
                                        <a href="{{ route('admin.hb837.export.template', 'xlsx') }}" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-file-excel"></i> Excel Template
                                        </a>
                                        <a href="{{ route('admin.hb837.export.template', 'csv') }}" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-file-csv"></i> CSV Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Area -->
                    <div id="notifications" class="mt-3"></div>

                    <!-- File Information Area -->
                    <div id="file-info" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Analysis Results (Hidden initially) -->
    <div class="row" id="analysis-results" style="display: none;">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> File Analysis Results
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">
                            <i class="fas fa-magic"></i> AI Analysis Complete
                        </span>
                    </div>
                </div>
                <div class="card-body" id="analysis-content">
                    <!-- Analysis results will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Data Preview (Hidden initially) -->
    <div class="row" id="preview-section" style="display: none;">
        <div class="col-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table"></i> Data Preview
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="confirm-import">
                            <i class="fas fa-check"></i> Confirm Import
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" id="cancel-import">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
                <div class="card-body" id="preview-content">
                    <!-- Preview table will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Import Results (Hidden initially) -->
    <div class="row" id="import-results" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle text-success"></i> Import Complete
                    </h3>
                </div>
                <div class="card-body" id="results-content">
                    <!-- Import results will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Smooth scrolling for all elements */
html {
    scroll-behavior: smooth;
}

/* Analysis results animation */
#analysis-results {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease-in-out;
}

#analysis-results.show {
    opacity: 1;
    transform: translateY(0);
}

/* Highlight effect when section comes into view */
#analysis-results.highlight {
    animation: highlightPulse 2s ease-in-out;
}

@keyframes highlightPulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0.1); }
    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}

.upload-zone {
    border: 3px dashed #007bff;
    border-radius: 10px;
    padding: 60px 20px;
    text-align: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-zone:hover {
    border-color: #0056b3;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    transform: translateY(-2px);
}

.upload-zone.dragover {
    border-color: #28a745;
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    transform: scale(1.02);
}

.upload-icon {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 1rem;
}

.upload-content h4 {
    color: #495057;
    margin-bottom: 1rem;
}

.info-panel {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.info-panel h5 {
    color: #495057;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.info-panel ul li {
    padding: 5px 0;
    border-bottom: 1px dotted #dee2e6;
}

.info-panel ul li:last-child {
    border-bottom: none;
}

.info-panel ul li i {
    width: 20px;
    text-align: center;
    margin-right: 10px;
}

/* File type indicators */
.file-type-excel {
    color: #28a745;
}

.file-type-csv {
    color: #17a2b8;
}

.file-info {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.data-stats {
    display: flex;
    justify-content: space-around;
    text-align: center;
    margin: 20px 0;
}

.data-stats .stat-item {
    flex: 1;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin: 0 5px;
}

.data-stats .stat-item .stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
}

.data-stats .stat-item .stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
}

.mapping-result {
    background: #e8f5e8;
    border: 1px solid #28a745;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
}

.mapping-result.warning {
    background: #fff3cd;
    border-color: #ffc107;
}

.mapping-result.error {
    background: #f8d7da;
    border-color: #dc3545;
}

/* Preview table styling */
.preview-table {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.preview-table table {
    margin-bottom: 0;
}

.preview-table th {
    background: #007bff;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Import action buttons */
.import-actions {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    margin-top: 20px;
}

.import-actions .btn {
    margin: 0 10px;
    min-width: 150px;
}

/* Enhanced Progress Section Styling */
#progress-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #007bff;
    border-radius: 12px;
    padding: 30px;
    margin: 20px 0;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
    position: relative;
    overflow: hidden;
}

#progress-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, transparent, #007bff, transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

#progress-section h4 {
    color: #495057;
    margin-bottom: 20px;
    font-weight: 600;
}

#progress-text {
    font-size: 1.1rem;
    color: #007bff;
    font-weight: 500;
    margin-bottom: 10px;
    display: block;
}

#progress-percentage {
    font-size: 1.3rem;
    font-weight: bold;
    color: #28a745;
    margin-left: 10px;
}

#progress-section .progress {
    height: 20px;
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 15px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

#progress-bar {
    background: linear-gradient(45deg, #007bff, #0056b3);
    height: 100%;
    transition: width 0.6s ease;
    position: relative;
    overflow: hidden;
}

#progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-image: linear-gradient(
        -45deg,
        rgba(255, 255, 255, 0.2) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, 0.2) 50%,
        rgba(255, 255, 255, 0.2) 75%,
        transparent 75%,
        transparent
    );
    background-size: 30px 30px;
    animation: progressStripes 1s linear infinite;
}

@keyframes progressStripes {
    0% { background-position: 0 0; }
    100% { background-position: 30px 0; }
}

/* Upload progress icon */
#progress-section .fa-spinner {
    font-size: 2rem;
    color: #007bff;
    margin-bottom: 15px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Results styling */
.result-summary {
    display: flex;
    justify-content: space-around;
    text-align: center;
    margin: 20px 0;
}

.result-item {
    flex: 1;
    padding: 20px;
    border-radius: 8px;
    margin: 0 10px;
}

.result-item.success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
}

.result-item.warning {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
}

.result-item.info {
    background: #cce5ff;
    border: 1px solid #b3d9ff;
}

.result-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.result-label {
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Responsive */
@media (max-width: 768px) {
    .upload-zone {
        padding: 40px 15px;
        min-height: 250px;
    }

    .data-stats,
    .result-summary {
        flex-direction: column;
    }

    .data-stats .stat-item,
    .result-item {
        margin: 5px 0;
    }
}
</style>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fas fa-question-circle"></i> Smart Import Help Guide</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-upload text-primary"></i> How to Upload</h5>
                        <ul>
                            <li><strong>Drag & Drop:</strong> Drag your file directly into the upload area</li>
                            <li><strong>Click to Browse:</strong> Click "Choose File" to select from your computer</li>
                            <li><strong>Email Support:</strong> Upload email files (.eml, .msg) with attachments</li>
                        </ul>

                        <h5><i class="fas fa-file text-success"></i> Supported Files</h5>
                        <ul>
                            <li><i class="fas fa-file-excel text-success"></i> Excel: .xlsx, .xls</li>
                            <li><i class="fas fa-file-csv text-info"></i> CSV: .csv</li>
                            <li><i class="fas fa-envelope text-warning"></i> Email: .eml, .msg</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-magic text-primary"></i> What It Does</h5>
                        <ul>
                            <li><i class="fas fa-search"></i> Automatically detects file format</li>
                            <li><i class="fas fa-link"></i> Maps your columns to HB837 fields</li>
                            <li><i class="fas fa-shield-alt"></i> Cleans and validates data</li>
                            <li><i class="fas fa-copy"></i> Detects and handles duplicates</li>
                            <li><i class="fas fa-eye"></i> Shows preview before importing</li>
                        </ul>

                        <h5><i class="fas fa-lightbulb text-warning"></i> Pro Tips</h5>
                        <ul>
                            <li>Use clear column headers like "Property Name", "Client"</li>
                            <li>Keep date formats consistent</li>
                            <li>Remove summary rows and notes</li>
                            <li>Each row should be one property</li>
                        </ul>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-download"></i> Need a Template?</h6>
                            <p class="mb-2">Download a properly formatted template to structure your data:</p>
                            <a href="{{ route('admin.hb837.export.template', 'xlsx') }}" class="btn btn-outline-success btn-sm mr-2">
                                <i class="fas fa-file-excel"></i> Excel Template
                            </a>
                            <a href="{{ route('admin.hb837.export.template', 'csv') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-file-csv"></i> CSV Template
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success">
                            <h6><i class="fas fa-heart"></i> Remember</h6>
                            <p class="mb-0">The Smart Import system is designed to be forgiving! Even if your data isn't perfectly formatted, the system will do its best to import it correctly. Always review the preview before final import.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="{{ asset('docs/SMART_IMPORT_USER_GUIDE.md') }}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Full User Guide
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    let currentFile = null;
    let analysisData = null;

    // File input change handler
    $('#file-input').on('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });

    // Enhanced drag and drop functionality
    const uploadZone = document.getElementById('upload-zone');

    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadZone.classList.add('dragover');

        // Show helpful message during drag
        const contentDiv = uploadZone.querySelector('.upload-content h4');
        if (contentDiv) {
            contentDiv.textContent = 'Drop your file here to start smart analysis!';
        }
    });

    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Only remove if we're actually leaving the zone
        if (!uploadZone.contains(e.relatedTarget)) {
            uploadZone.classList.remove('dragover');
            const contentDiv = uploadZone.querySelector('.upload-content h4');
            if (contentDiv) {
                contentDiv.textContent = 'Drop your file here or click to browse';
            }
        }
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadZone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            // Support multiple files - take the first valid one
            for (let i = 0; i < files.length; i++) {
                if (isValidFileType(files[i])) {
                    handleFileSelection(files[i]);
                    break;
                }
            }
        }
    });

    // Validate file type with enhanced support
    function isValidFileType(file) {
        const validExtensions = ['.xlsx', '.xls', '.csv', '.eml', '.msg'];
        const validMimeTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'text/csv',
            'application/octet-stream', // For .eml and .msg files
            'message/rfc822'
        ];

        const fileName = file.name.toLowerCase();
        const isValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
        const isValidMime = validMimeTypes.includes(file.type);

        return isValidExtension || isValidMime;
    }

    // Click to upload
    uploadZone.addEventListener('click', function() {
        document.getElementById('file-input').click();
    });

    function handleFileSelection(file) {
        currentFile = file;

        // Enhanced file validation
        if (!isValidFileType(file)) {
            showNotification('Invalid file type. Please select Excel (.xlsx, .xls), CSV (.csv), or email files (.eml, .msg).', 'error');
            return;
        }

        // File size validation (100MB limit)
        const maxSizeMB = 100;
        if (file.size > maxSizeMB * 1024 * 1024) {
            showNotification(`File too large. Maximum size is ${maxSizeMB}MB.`, 'error');
            return;
        }

        // Show file info to user
        const fileInfo = `
            <div class="alert alert-info">
                <strong><i class="fas fa-file"></i> ${file.name}</strong><br>
                <small>Size: ${formatFileSize(file.size)} | Type: ${getFileTypeDescription(file)}</small>
            </div>
        `;
        $('#file-info').html(fileInfo);

        // Show progress
        showProgress('Analyzing file structure and detecting format...');

        // Start intelligent file analysis
        analyzeFile(file);
    }

    function getFileTypeDescription(file) {
        const fileName = file.name.toLowerCase();
        if (fileName.endsWith('.xlsx')) return 'Excel Workbook (Modern)';
        if (fileName.endsWith('.xls')) return 'Excel Workbook (Legacy)';
        if (fileName.endsWith('.csv')) return 'Comma Separated Values';
        if (fileName.endsWith('.eml')) return 'Email Message';
        if (fileName.endsWith('.msg')) return 'Outlook Message';
        return 'Unknown Format';
    }

    function showNotification(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : type === 'success' ? 'alert-success' : 'alert-info';
        const iconClass = type === 'error' ? 'fa-exclamation-triangle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle';

        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas ${iconClass}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;

        $('#notifications').html(notification);

        // Auto-hide success/info notifications after 5 seconds
        if (type !== 'error') {
            setTimeout(() => {
                $('#notifications .alert').fadeOut();
            }, 5000);
        }
    }

    function analyzeFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Show detailed progress steps
        const progressSteps = [
            'Uploading file...',
            'Detecting file format...',
            'Reading data structure...',
            'Analyzing columns...',
            'Mapping fields intelligently...',
            'Validating data quality...',
            'Preparing preview...'
        ];

        let currentStep = 0;

        $.ajax({
            url: '{{ route("admin.hb837.import.analyze") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = Math.round((e.loaded / e.total) * 50); // Upload is 50% of total
                        updateProgress(percentComplete, progressSteps[0]);

                        // Simulate analysis steps after upload
                        if (percentComplete >= 50) {
                            simulateAnalysisSteps();
                        }
                    }
                });
                return xhr;
            },
            success: function(response) {
                analysisData = response;
                updateProgress(100, 'Analysis complete! 🎉');

                // Show success notification
                showNotification(
                    `Successfully analyzed ${response.stats?.total_rows || 0} rows with ${response.stats?.columns || 0} columns. Ready for import!`,
                    'success'
                );

                setTimeout(function() {
                    hideProgress();
                    showAnalysisResults(response);
                }, 1000);
            },
            error: function(xhr, status, error) {
                hideProgress();
                let errorMessage = 'Failed to analyze file. Please check the format and try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 413) {
                    errorMessage = 'File is too large. Please try a smaller file.';
                } else if (xhr.status === 422) {
                    errorMessage = 'Invalid file format or corrupted file.';
                }

                showNotification(errorMessage, 'error');
            }
        });

        // Simulate analysis steps for better UX
        function simulateAnalysisSteps() {
            if (currentStep < progressSteps.length - 1) {
                currentStep++;
                const progress = 50 + (currentStep * 7); // Start from 50% after upload
                updateProgress(Math.min(progress, 95), progressSteps[currentStep]);

                setTimeout(simulateAnalysisSteps, 300);
            }
        }
    }

    function showProgress(text) {
        $('#progress-section').show();
        $('#progress-text').text(text);
        $('#progress-percentage').text('0%');
        $('#progress-bar').css('width', '0%');
    }

    function updateProgress(percentage, text) {
        $('#progress-text').text(text);
        $('#progress-percentage').text(percentage + '%');
        $('#progress-bar').css('width', percentage + '%');
    }

    function hideProgress() {
        $('#progress-section').hide();
    }

    function showAnalysisResults(data) {
        // Show analysis results with smooth animation
        const analysisSection = $('#analysis-results');
        analysisSection.show();
        
        // Add show class for fade-in animation
        setTimeout(function() {
            analysisSection.addClass('show');
        }, 50);

        // Smooth scroll to analysis results section with enhanced timing
        setTimeout(function() {
            const analysisElement = document.getElementById('analysis-results');
            if (analysisElement) {
                // Add highlight effect
                analysisSection.addClass('highlight');
                
                // Smooth scroll with custom offset for better viewing
                const elementTop = analysisElement.offsetTop;
                const offset = 20; // Small offset from top
                
                window.scrollTo({
                    top: elementTop - offset,
                    behavior: 'smooth'
                });
                
                // Remove highlight effect after animation
                setTimeout(function() {
                    analysisSection.removeClass('highlight');
                }, 2000);
            }
        }, 400); // Delay to let the fade-in animation complete

        let html = `
            <div class="file-info">
                <h5><i class="fas fa-file ${getFileTypeClass(currentFile.name)}"></i> ${currentFile.name}</h5>
                <p><strong>Size:</strong> ${formatFileSize(currentFile.size)} |
                   <strong>Type:</strong> ${currentFile.type || 'Unknown'} |
                   <strong>Detected:</strong> ${data.detection.type}</p>
            </div>

            <div class="data-stats">
                <div class="stat-item">
                    <div class="stat-number">${data.stats.total_rows}</div>
                    <div class="stat-label">Total Rows</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${data.stats.valid_rows}</div>
                    <div class="stat-label">Valid Rows</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${data.stats.columns}</div>
                    <div class="stat-label">Columns</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${data.stats.new_records}</div>
                    <div class="stat-label">New Records</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">${data.stats.updates}</div>
                    <div class="stat-label">Updates</div>
                </div>
            </div>

            <div class="mapping-controls">
                <h6><i class="fas fa-map"></i> Column Mapping</h6>
                <div class="form-check form-check-inline mb-2">
                    <input class="form-check-input" type="checkbox" id="hideUnmapped" checked>
                    <label class="form-check-label" for="hideUnmapped">
                        Hide unmapped fields (0% confidence)
                    </label>
                </div>
            </div>
            <div class="mapping-results">
        `;

        // Add column mappings with filtering
        data.mapping.forEach(function(map) {
            // Skip unmapped fields if hiding is enabled
            const hideUnmapped = true; // Default to hiding unmapped fields
            if (hideUnmapped && map.confidence === 0) {
                return; // Skip this mapping
            }
            
            const cssClass = map.confidence > 0.8 ? 'mapping-result' :
                           map.confidence > 0.5 ? 'mapping-result warning' : 'mapping-result error';
           
            html += `
                <div class="${cssClass}">
                    <strong>${map.source_column}</strong> → ${map.target_field}
                    <span class="float-right">
                        <span class="badge badge-${map.confidence > 0.8 ? 'success' : map.confidence > 0.5 ? 'warning' : 'danger'}">
                            ${Math.round(map.confidence * 100)}% confidence
                        </span>                    
                    </span>
                </div>
            `;
        });
        
        html += '</div>'; // Close mapping-results container

        // Add warnings if any
        if (data.warnings.length > 0) {
            html += '<h6 class="mt-3"><i class="fas fa-exclamation-triangle text-warning"></i> Warnings</h6>';
            data.warnings.forEach(function(warning) {
                html += `<div class="alert alert-warning">${warning}</div>`;
            });
        }

        // Add import actions
        html += `
            <div class="import-actions">
                <button type="button" class="btn btn-primary btn-lg" onclick="showPreview()">
                    <i class="fas fa-eye"></i> Preview Data
                </button>
                <button type="button" class="btn btn-success btn-lg" onclick="confirmImport()">
                    <i class="fas fa-check"></i> Import Now
                </button>
                <button type="button" class="btn btn-secondary" onclick="resetImport()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        `;

        $('#analysis-content').html(html);
        
        // Add event listener for the hide unmapped checkbox
        $('#hideUnmapped').on('change', function() {
            renderMappings(data);
        });
    }
    
    function renderMappings(data) {
        const hideUnmapped = $('#hideUnmapped').is(':checked');
        let mappingHtml = '';
        
        data.mapping.forEach(function(map) {
            // Skip unmapped fields if hiding is enabled
            if (hideUnmapped && map.confidence === 0) {
                return; // Skip this mapping
            }
            
            const cssClass = map.confidence > 0.8 ? 'mapping-result' :
                           map.confidence > 0.5 ? 'mapping-result warning' : 'mapping-result error';

            mappingHtml += `
                <div class="${cssClass}">
                    <strong>${map.source_column}</strong> → ${map.target_field}
                    <span class="float-right">
                        <span class="badge badge-${map.confidence > 0.8 ? 'success' : map.confidence > 0.5 ? 'warning' : 'danger'}">
                            ${Math.round(map.confidence * 100)}% confidence
                        </span>
                    </span>
                </div>
            `;
        });
        
        // Update only the mappings container
        $('.mapping-results').html(mappingHtml);
    }

    window.showPreview = function() {
        if (!analysisData) return;

        showProgress('Loading preview...');

        $.ajax({
            url: '{{ route("admin.hb837.import.preview") }}',
            method: 'POST',
            data: {
                file_id: analysisData.file_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideProgress();
                showPreviewSection(response);
            },
            error: function(xhr, status, error) {
                hideProgress();
                alert('Error loading preview: ' + (xhr.responseJSON?.message || error));
            }
        });
    };

    function showPreviewSection(data) {
        $('#preview-section').show();

        let html = `
            <div class="preview-table">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
        `;

        // Add headers
        data.headers.forEach(function(header) {
            html += `<th>${header}</th>`;
        });
        html += '</tr></thead><tbody>';

        // Add preview rows (limit to first 10)
        data.preview_rows.slice(0, 10).forEach(function(row) {
            html += '<tr>';
            row.forEach(function(cell) {
                html += `<td>${cell || ''}</td>`;
            });
            html += '</tr>';
        });

        html += '</tbody></table>';

        if (data.preview_rows.length > 10) {
            html += `<p class="text-center text-muted">Showing first 10 rows of ${data.total_rows} total rows.</p>`;
        }

        html += '</div>';

        $('#preview-content').html(html);
    }

    window.confirmImport = function() {
        if (!analysisData) return;

        if (!confirm('Are you sure you want to import this data? This action cannot be undone.')) {
            return;
        }

        // Hide the preview section and show loading state
        $('#preview-section').hide();
        showProgress('Preparing import...');
        
        // Add a more detailed progress simulation for upload
        let progress = 0;
        const progressSteps = [
            { percent: 10, text: 'Validating data...' },
            { percent: 25, text: 'Processing records...' },
            { percent: 40, text: 'Importing data...' },
            { percent: 60, text: 'Updating database...' },
            { percent: 80, text: 'Finalizing import...' },
            { percent: 95, text: 'Almost done...' }
        ];
        
        let stepIndex = 0;
        const progressInterval = setInterval(() => {
            if (stepIndex < progressSteps.length) {
                const step = progressSteps[stepIndex];
                updateProgress(step.percent, step.text);
                stepIndex++;
            }
        }, 500);

        $.ajax({
            url: '{{ route("admin.hb837.import.execute") }}',
            method: 'POST',
            data: {
                file_id: analysisData.file_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                clearInterval(progressInterval);
                updateProgress(100, 'Import completed!');
                
                // Brief delay to show completion
                setTimeout(() => {
                    hideProgress();
                    showImportResults(response);
                }, 1000);

                // Auto-redirect with countdown after showing results
                if (response.redirect_url) {
                    let countdown = 3;
                    const countdownElement = document.getElementById('countdown');

                    const timer = setInterval(function() {
                        countdown--;
                        if (countdownElement) {
                            countdownElement.textContent = countdown;
                        }

                        if (countdown <= 0) {
                            clearInterval(timer);
                            window.location.href = response.redirect_url;
                        }
                    }, 1000);
                }
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                hideProgress();
                $('#preview-section').show(); // Show preview again on error
                alert('Error during import: ' + (xhr.responseJSON?.message || error));
            }
        });
    };

    function showImportResults(data) {
        $('#import-results').show();

        let html = `
            <div class="result-summary">
                <div class="result-item success">
                    <div class="result-number text-success">${data.imported}</div>
                    <div class="result-label">Imported</div>
                </div>
                <div class="result-item warning">
                    <div class="result-number text-warning">${data.updated}</div>
                    <div class="result-label">Updated</div>
                </div>
                <div class="result-item info">
                    <div class="result-number text-info">${data.skipped}</div>
                    <div class="result-label">Skipped</div>
                </div>
            </div>

            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <strong>Import completed successfully!</strong>
                <br>Redirecting to HB837 data view in <span id="countdown">3</span> seconds...
                <br><small>You can click the button below to go now.</small>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('admin.hb837.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-table"></i> View HB837 Data Now
                </a>
                <button type="button" class="btn btn-secondary" onclick="resetImport()">
                    <i class="fas fa-upload"></i> Import Another File
                </button>
            </div>
        `;

        if (data.errors && data.errors.length > 0) {
            html += '<h6 class="mt-4"><i class="fas fa-exclamation-circle text-danger"></i> Errors</h6>';
            data.errors.forEach(function(error) {
                html += `<div class="alert alert-danger">${error}</div>`;
            });
        }

        $('#results-content').html(html);
    }

    window.resetImport = function() {
        currentFile = null;
        analysisData = null;

        // Hide all sections
        $('#progress-section, #analysis-results, #preview-section, #import-results').hide();

        // Reset file input
        $('#file-input').val('');

        // Scroll to top
        $('html, body').animate({scrollTop: 0}, 500);
    };

    // Cancel and confirm import buttons
    $('#cancel-import').on('click', resetImport);
    $('#confirm-import').on('click', confirmImport);

    // Helper functions
    function getFileTypeClass(filename) {
        if (filename.toLowerCase().endsWith('.xlsx') || filename.toLowerCase().endsWith('.xls')) {
            return 'file-type-excel';
        } else if (filename.toLowerCase().endsWith('.csv')) {
            return 'file-type-csv';
        }
        return '';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@stop
