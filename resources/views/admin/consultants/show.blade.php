@extends('adminlte::page')

@section('title', 'Consultant Details - ProjectTracker Fresh')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ $consultant->full_name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.consultants.index') }}">Consultants</a></li>
                <li class="breadcrumb-item active">{{ $consultant->full_name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $consultant->full_name }} - Consultant Record</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.consultants.edit', $consultant->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.consultants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="consultantTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $tab === 'information' ? 'active' : '' }}"
                               id="information-tab" data-toggle="tab" href="#information" role="tab"
                               aria-controls="information" aria-selected="{{ $tab === 'information' ? 'true' : 'false' }}">
                                <i class="fas fa-user"></i> Consultant Information
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $tab === 'active-assignments' ? 'active' : '' }}"
                               id="active-assignments-tab" data-toggle="tab" href="#active-assignments" role="tab"
                               aria-controls="active-assignments" aria-selected="{{ $tab === 'active-assignments' ? 'true' : 'false' }}">
                                <i class="fas fa-tasks"></i> Active Assignments <span class="badge badge-primary">{{ $activeAssignments->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $tab === 'completed-assignments' ? 'active' : '' }}"
                               id="completed-assignments-tab" data-toggle="tab" href="#completed-assignments" role="tab"
                               aria-controls="completed-assignments" aria-selected="{{ $tab === 'completed-assignments' ? 'true' : 'false' }}">
                                <i class="fas fa-check-circle"></i> Completed Assignments <span class="badge badge-success">{{ $completedAssignments->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $tab === 'files' ? 'active' : '' }}"
                               id="files-tab" data-toggle="tab" href="#files" role="tab"
                               aria-controls="files" aria-selected="{{ $tab === 'files' ? 'true' : 'false' }}">
                                <i class="fas fa-file-alt"></i> Files <span class="badge badge-info">{{ $consultant->files->count() }}</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="consultantTabsContent">
                        <!-- Consultant Information Tab -->
                        <div class="tab-pane fade {{ $tab === 'information' ? 'show active' : '' }}"
                             id="information" role="tabpanel" aria-labelledby="information-tab">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h5>Personal Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $consultant->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><a href="mailto:{{ $consultant->email }}">{{ $consultant->email }}</a></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Company:</strong></td>
                                            <td>{{ $consultant->dba_company_name ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mailing Address:</strong></td>
                                            <td>{{ $consultant->mailing_address ?: 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5>Professional Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>FCP Expiration:</strong></td>
                                            <td>
                                                @if($consultant->fcp_expiration_date)
                                                    {{ $consultant->fcp_expiration_date->format('M d, Y') }}
                                                    @php
                                                        $daysUntilExpiry = now()->diffInDays($consultant->fcp_expiration_date, false);
                                                    @endphp
                                                    @if($daysUntilExpiry < 0)
                                                        <span class="badge badge-danger ml-2">Expired</span>
                                                    @elseif($daysUntilExpiry <= 30)
                                                        <span class="badge badge-warning ml-2">Expires in {{ $daysUntilExpiry }} days</span>
                                                    @else
                                                        <span class="badge badge-success ml-2">Valid</span>
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Light Meter:</strong></td>
                                            <td>{{ $consultant->assigned_light_meter ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>LM NIST Expiration:</strong></td>
                                            <td>
                                                @if($consultant->lm_nist_expiration_date)
                                                    {{ $consultant->lm_nist_expiration_date->format('M d, Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bonus Rate:</strong></td>
                                            <td>
                                                @if($consultant->subcontractor_bonus_rate)
                                                    ${{ number_format($consultant->subcontractor_bonus_rate, 2) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($consultant->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Notes</h5>
                                    <div class="card card-body bg-light">
                                        {!! nl2br(e($consultant->notes)) !!}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Active Assignments Tab -->
                        <div class="tab-pane fade {{ $tab === 'active-assignments' ? 'show active' : '' }}"
                             id="active-assignments" role="tabpanel" aria-labelledby="active-assignments-tab">
                            <div class="mt-3">
                                @if($activeAssignments->count() > 0)
                                    <table class="table table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Property Name</th>
                                                <th>Macro Client</th>
                                                <th>Date of Scheduled Inspection</th>
                                                <th>Report Status</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeAssignments as $assignment)
                                            <tr>
                                                <td>{{ $assignment->property_name }}</td>
                                                <td>{{ $assignment->macro_client ?: 'N/A' }}</td>
                                                <td>
                                                    @if($assignment->scheduled_date_of_inspection)
                                                        {{ $assignment->scheduled_date_of_inspection->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">Not scheduled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($assignment->report_status)
                                                        @case('not-started')
                                                            <span class="badge badge-secondary">Not Started</span>
                                                            @break
                                                        @case('in-progress')
                                                            <span class="badge badge-primary">In Progress</span>
                                                            @break
                                                        @case('in-review')
                                                            <span class="badge badge-warning">In Review</span>
                                                            @break
                                                        @default
                                                            <span class="badge badge-light">{{ ucfirst($assignment->report_status ?? 'Unknown') }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.hb837.show', $assignment->id) }}"
                                                       class="btn btn-sm btn-primary" title="View Property">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> This consultant currently has no active assignments.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Completed Assignments Tab -->
                        <div class="tab-pane fade {{ $tab === 'completed-assignments' ? 'show active' : '' }}"
                             id="completed-assignments" role="tabpanel" aria-labelledby="completed-assignments-tab">
                            <div class="mt-3">
                                @if($completedAssignments->count() > 0)
                                    <table class="table table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Property Name</th>
                                                <th>Macro Client</th>
                                                <th>Date of Scheduled Inspection</th>
                                                <th>Report Status</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($completedAssignments as $assignment)
                                            <tr>
                                                <td>{{ $assignment->property_name }}</td>
                                                <td>{{ $assignment->macro_client ?: 'N/A' }}</td>
                                                <td>
                                                    @if($assignment->scheduled_date_of_inspection)
                                                        {{ $assignment->scheduled_date_of_inspection->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">Not scheduled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">Completed</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.hb837.show', $assignment->id) }}"
                                                       class="btn btn-sm btn-primary" title="View Property">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> This consultant has no completed assignments yet.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Files Tab -->
                        <div class="tab-pane fade {{ $tab === 'files' ? 'show active' : '' }}"
                             id="files" role="tabpanel" aria-labelledby="files-tab">
                            <div class="mt-3">
                                <!-- File Upload Form -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Upload New File</h5>
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('admin.consultants.files.upload', $consultant->id) }}"
                                                      method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="file">File (PDF, DOC, DOCX, JPG, PNG - Max 10MB)</label>
                                                                <input type="file" class="form-control-file @error('file') is-invalid @enderror"
                                                                       id="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                                                @error('file')
                                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="description">Description (Optional)</label>
                                                                <input type="text" class="form-control @error('description') is-invalid @enderror"
                                                                       id="description" name="description" placeholder="Enter file description">
                                                                @error('description')
                                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-upload"></i> Upload File
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Files List -->
                                @if($consultant->files->count() > 0)
                                    <table class="table table-striped table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>File Name</th>
                                                <th>Description</th>
                                                <th>Size</th>
                                                <th>Uploaded</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($consultant->files as $file)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    {{ $file->original_filename }}
                                                </td>
                                                <td>{{ $file->description ?: 'N/A' }}</td>
                                                <td>{{ $file->file_size_human }}</td>
                                                <td>{{ $file->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.consultants.files.download', $file->id) }}"
                                                       class="btn btn-sm btn-primary" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <form action="{{ route('admin.consultants.files.delete', $file->id) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this file?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No files uploaded for this consultant yet.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Using the same color scheme as HB837 property records */
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.02);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.035);
}

.bg-light {
    background-color: #f8f9fa !important;
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #007bff;
}
</style>
@stop
