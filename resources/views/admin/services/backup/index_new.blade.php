@extends('layouts.admin')

@section('title', 'Database Backup & Services')

@section('content_header_content')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Database Backup & Services</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Backup & Services</li>
            </ol>
        </div>
    </div>
@stop

@section('main_content')
    <!-- System Status Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_backups'] ?? 0 }}</h3>
                    <p>Total Backups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="#backup-history" class="small-box-footer">
                    View History <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $systemStats['db_size'] ?? 'N/A' }}</h3>
                    <p>Database Size</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <a href="#system-info" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $systemStats['hb837_records'] ?? 0 }}</h3>
                    <p>HB837 Records</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
                <a href="{{ route('admin.hb837.index') }}" class="small-box-footer">
                    View Records <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['recent_backups'] ?? 0 }}</h3>
                    <p>Recent Backups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#recent-activity" class="small-box-footer">
                    View Activity <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i>
                        Backup Operations
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-download"></i>
                                        Create Backup
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted">Generate a new database backup with selected tables.</p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#backupModal">
                                        <i class="fas fa-database"></i> Create Backup
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-upload"></i>
                                        Import Data
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted">Import HB837 data from Excel files.</p>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#importModal">
                                        <i class="fas fa-file-excel"></i> Import Data
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-undo"></i>
                                        Restore Database
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted">Restore database from a previous backup.</p>
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#restoreModal">
                                        <i class="fas fa-undo"></i> Restore Backup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup History -->
    <div class="row" id="backup-history">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Backup History
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Backup Name</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>File Size</th>
                                    <th>Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $backup)
                                    <tr>
                                        <td>
                                            <strong>{{ $backup->name ?: 'Backup_' . $backup->id }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $backup->filename }}</small>
                                        </td>
                                        <td>
                                            {{ $backup->user->name ?? 'System' }}
                                        </td>
                                        <td>
                                            {{ $backup->created_at->format('M d, Y H:i') }}
                                            <br>
                                            <small class="text-muted">{{ $backup->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if($backup->file_size)
                                                {{ number_format($backup->file_size / 1024, 2) }} KB
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($backup->status === 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($backup->status === 'failed')
                                                <span class="badge badge-danger">Failed</span>
                                            @else
                                                <span class="badge badge-warning">Processing</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if($backup->status === 'completed')
                                                    <a href="{{ route('admin.hb837.backup.download', $backup->filename) }}" 
                                                       class="btn btn-info btn-sm" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-warning btn-sm" 
                                                            data-toggle="modal" data-target="#restoreModal" 
                                                            data-backup-id="{{ $backup->uuid }}" 
                                                            title="Restore">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                @endif
                                                <form action="{{ route('admin.hb837.backup.delete_file', $backup->id) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm btn-delete" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-database fa-3x mb-3"></i>
                                            <br>
                                            No backups found. Create your first backup to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($backups->hasPages())
                    <div class="card-footer">
                        {{ $backups->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Import Activity -->
    @if(isset($importAudits) && $importAudits->count() > 0)
    <div class="row mt-3" id="recent-activity">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i>
                        Recent Import Activity
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Import Date</th>
                                    <th>User</th>
                                    <th>Records Imported</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importAudits as $audit)
                                    <tr>
                                        <td>{{ $audit->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $audit->user->name ?? 'System' }}</td>
                                        <td>{{ $audit->records_processed ?? 0 }}</td>
                                        <td>
                                            <span class="badge badge-{{ $audit->status === 'success' ? 'success' : 'danger' }}">
                                                {{ ucfirst($audit->status) }}
                                            </span>
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
    @endif

    <!-- Include Modals -->
    @include('partials.modals.backup_modal')
    @include('partials.modals.restore_modal')
    @include('partials.modals.export_import_modal')
@stop

@section('custom_css')
    <style>
        .card-outline {
            border-top: 3px solid;
        }
        
        .card-outline.card-primary {
            border-top-color: #007bff;
        }
        
        .card-outline.card-success {
            border-top-color: #28a745;
        }
        
        .card-outline.card-warning {
            border-top-color: #ffc107;
        }
        
        .small-box .icon {
            top: -10px;
            font-size: 70px;
        }
        
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
@stop

@section('custom_js')
    <script>
        $(document).ready(function() {
            // Handle restore modal data
            $('#restoreModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var backupId = button.data('backup-id');
                if (backupId) {
                    $(this).find('#restore_backup_id').val(backupId);
                }
            });
            
            // Confirm deletion
            $(document).on('click', '.btn-delete', function(e) {
                if (!confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Auto-refresh status every 30 seconds
            setInterval(function() {
                if (!$('.modal').hasClass('show')) { // Don't refresh if modal is open
                    $('.badge-warning').closest('tr').each(function() {
                        // Only refresh processing backups
                        if ($(this).find('.badge-warning').text().trim() === 'Processing') {
                            window.location.reload();
                        }
                    });
                }
            }, 30000);
        });
    </script>
@stop
