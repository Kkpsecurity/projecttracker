@extends('adminlte::page')

@section('title', 'Edit HB837 Record - ProjectTracker Fresh')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit HB837 Record</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item active">Edit Record</li>
            </ol>
        </div>
    </div>
@stop

@section('css')
<style>
.nav-tabs .nav-link {
    color: #495057;
    background-color: transparent;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    background-color: #f8f9fa;
    transform: translateY(-1px);
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background-color: #fff;
    border-color: #007bff #007bff #fff;
    font-weight: 600;
    box-shadow: 0 -2px 8px rgba(0, 123, 255, 0.1);
}

.nav-tabs .nav-link.active i {
    color: #007bff !important;
}

.tab-content {
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.25rem 0.25rem;
    padding: 0;
    min-height: 400px;
}

.tab-pane {
    padding: 1.5rem;
}

.nav-tabs {
    margin-bottom: 0;
    border-bottom: 2px solid #dee2e6;
}

.tab-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 0.375rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #007bff;
}

.tab-title {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2c3e50;
}

.tab-title i {
    margin-right: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.card {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
    border: none;
}

.badge {
    font-size: 0.75rem;
}

/* Active tab indicator */
.nav-tabs .nav-link.active::before {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 2px 2px 0 0;
}

.nav-tabs .nav-item {
    position: relative;
}

/* Enhanced form styling */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}
</style>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit: {{ $hb837->property_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.hb837.show', $hb837->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('admin.hb837.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.hb837.update', $hb837->id) }}" method="POST" id="hb837-edit-form">
                        @csrf
                        @method('PUT')

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="editTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'general' ? 'active' : '' }}" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="{{ $tab === 'general' ? 'true' : 'false' }}">
                                    <i class="fas fa-home"></i> General
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'address' ? 'active' : '' }}" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="{{ $tab === 'address' ? 'true' : 'false' }}">
                                    <i class="fas fa-map-marker-alt"></i> Address
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'contact' ? 'active' : '' }}" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="{{ $tab === 'contact' ? 'true' : 'false' }}">
                                    <i class="fas fa-users"></i> Contact
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'financial' ? 'active' : '' }}" id="financial-tab" data-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="{{ $tab === 'financial' ? 'true' : 'false' }}">
                                    <i class="fas fa-dollar-sign"></i> Financial
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'notes' ? 'active' : '' }}" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="{{ $tab === 'notes' ? 'true' : 'false' }}">
                                    <i class="fas fa-sticky-note"></i> Notes
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'files' ? 'active' : '' }}" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="{{ $tab === 'files' ? 'true' : 'false' }}">
                                    <i class="fas fa-file-alt"></i> Files
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'maps' ? 'active' : '' }}" id="maps-tab" data-toggle="tab" href="#maps" role="tab" aria-controls="maps" aria-selected="{{ $tab === 'maps' ? 'true' : 'false' }}">
                                    <i class="fas fa-map"></i> Maps
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $tab === 'custom' ? 'active' : '' }}" id="custom-tab" data-toggle="tab" href="#custom" role="tab" aria-controls="custom" aria-selected="{{ $tab === 'custom' ? 'true' : 'false' }}">
                                    <i class="fas fa-wrench"></i> Custom Fields
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content" id="editTabsContent">
                            <!-- General Tab -->
                            <div class="tab-pane fade {{ $tab === 'general' ? 'show active' : '' }}" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-home text-primary"></i> General Information
                                    </h4>
                                    <p class="text-muted">Basic property details and information</p>
                                </div>
                                <div class="row mt-3">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="property_name">Property Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('property_name') is-invalid @enderror"
                                                   id="property_name" name="property_name" value="{{ old('property_name', $hb837->property_name) }}" required>
                                            @error('property_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="address_general">Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                                   id="address_general" name="address" value="{{ old('address', $hb837->address) }}" required>
                                            @error('address')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city">City</label>
                                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                           id="city" name="city" value="{{ old('city', $hb837->city) }}">
                                                    @error('city')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="county">County</label>
                                                    <input type="text" class="form-control @error('county') is-invalid @enderror"
                                                           id="county" name="county" value="{{ old('county', $hb837->county) }}">
                                                    @error('county')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="state">State</label>
                                                    <input type="text" class="form-control @error('state') is-invalid @enderror"
                                                           id="state" name="state" value="{{ old('state', $hb837->state) }}" maxlength="2">
                                                    @error('state')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="zip">ZIP</label>
                                                    <input type="text" class="form-control @error('zip') is-invalid @enderror"
                                                           id="zip" name="zip" value="{{ old('zip', $hb837->zip) }}">
                                                    @error('zip')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="units">Units</label>
                                            <input type="number" class="form-control @error('units') is-invalid @enderror"
                                                   id="units" name="units" value="{{ old('units', $hb837->units) }}">
                                            @error('units')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="notes_general">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes_general" name="notes" rows="4">{{ old('notes', $hb837->notes) }}</textarea>
                                            @error('notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="macro_client">Macro Client</label>
                                            <input type="text" class="form-control @error('macro_client') is-invalid @enderror"
                                                   id="macro_client" name="macro_client" value="{{ old('macro_client', $hb837->macro_client) }}">
                                            @error('macro_client')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="macro_email">Macro Client Email</label>
                                            <input type="email" class="form-control @error('macro_email') is-invalid @enderror"
                                                   id="macro_email" name="macro_email" value="{{ old('macro_email', $hb837->macro_email) }}">
                                            @error('macro_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="management_company">Management Company</label>
                                            <input type="text" class="form-control @error('management_company') is-invalid @enderror"
                                                   id="management_company" name="management_company" value="{{ old('management_company', $hb837->management_company) }}">
                                            @error('management_company')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="scheduled_date_of_inspection">Date of Scheduled Inspection</label>
                                            <input type="date" class="form-control @error('scheduled_date_of_inspection') is-invalid @enderror"
                                                   id="scheduled_date_of_inspection" name="scheduled_date_of_inspection"
                                                   value="{{ old('scheduled_date_of_inspection', $hb837->scheduled_date_of_inspection ? $hb837->scheduled_date_of_inspection->format('Y-m-d') : '') }}">
                                            @error('scheduled_date_of_inspection')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="report_status">Report Status</label>
                                            <select class="form-control @error('report_status') is-invalid @enderror" id="report_status" name="report_status">
                                                <option value="">Select Status</option>
                                                <option value="not-started" {{ old('report_status', $hb837->report_status) == 'not-started' ? 'selected' : '' }}>Not Started</option>
                                                <option value="in-progress" {{ old('report_status', $hb837->report_status) == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="in-review" {{ old('report_status', $hb837->report_status) == 'in-review' ? 'selected' : '' }}>In Review</option>
                                                <option value="completed" {{ old('report_status', $hb837->report_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                            @error('report_status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="assigned_consultant_id">Assigned Consultant</label>
                                            <select class="form-control @error('assigned_consultant_id') is-invalid @enderror" id="assigned_consultant_id" name="assigned_consultant_id">
                                                <option value="">Select Consultant</option>
                                                @foreach($consultants as $consultant)
                                                    <option value="{{ $consultant->id }}" {{ old('assigned_consultant_id', $hb837->assigned_consultant_id) == $consultant->id ? 'selected' : '' }}>
                                                        {{ $consultant->first_name }} {{ $consultant->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('assigned_consultant_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contacts Tab -->
                            <div class="tab-pane fade {{ $tab === 'contact' ? 'show active' : '' }}" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-users text-primary"></i> Contact Information
                                    </h4>
                                    <p class="text-muted">Property manager and key contact details</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h5>Property Manager</h5>
                                        <div class="form-group">
                                            <label for="property_manager_name">Property Manager Name</label>
                                            <input type="text" class="form-control @error('property_manager_name') is-invalid @enderror"
                                                   id="property_manager_name" name="property_manager_name" value="{{ old('property_manager_name', $hb837->property_manager_name) }}">
                                            @error('property_manager_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="property_manager_email">Property Manager Email</label>
                                            <input type="email" class="form-control @error('property_manager_email') is-invalid @enderror"
                                                   id="property_manager_email" name="property_manager_email" value="{{ old('property_manager_email', $hb837->property_manager_email) }}">
                                            @error('property_manager_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone" name="phone" value="{{ old('phone', $hb837->phone) }}">
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Regional Manager</h5>
                                        <div class="form-group">
                                            <label for="regional_manager_name">Regional Manager Name</label>
                                            <input type="text" class="form-control @error('regional_manager_name') is-invalid @enderror"
                                                   id="regional_manager_name" name="regional_manager_name" value="{{ old('regional_manager_name', $hb837->regional_manager_name) }}">
                                            @error('regional_manager_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="regional_manager_email">Regional Manager Email</label>
                                            <input type="email" class="form-control @error('regional_manager_email') is-invalid @enderror"
                                                   id="regional_manager_email" name="regional_manager_email" value="{{ old('regional_manager_email', $hb837->regional_manager_email) }}">
                                            @error('regional_manager_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="macro_contact">Macro Contact</label>
                                            <input type="text" class="form-control @error('macro_contact') is-invalid @enderror"
                                                   id="macro_contact" name="macro_contact" value="{{ old('macro_contact', $hb837->macro_contact) }}">
                                            @error('macro_contact')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Tab -->
                            <div class="tab-pane fade {{ $tab === 'financial' ? 'show active' : '' }}" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-dollar-sign text-primary"></i> Financial Information
                                    </h4>
                                    <p class="text-muted">Pricing, billing, and financial details</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quoted_price">Quoted Price</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control @error('quoted_price') is-invalid @enderror"
                                                       id="quoted_price" name="quoted_price" value="{{ old('quoted_price', (string)$hb837->quoted_price) }}">
                                                @error('quoted_price')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="sub_fees_estimated_expenses">Sub Fees & Expenses</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control @error('sub_fees_estimated_expenses') is-invalid @enderror"
                                                       id="sub_fees_estimated_expenses" name="sub_fees_estimated_expenses" value="{{ old('sub_fees_estimated_expenses', (string)$hb837->sub_fees_estimated_expenses) }}">
                                                @error('sub_fees_estimated_expenses')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="billing_req_sent">Billing Request Submitted</label>
                                            <input type="date" class="form-control @error('billing_req_sent') is-invalid @enderror"
                                                   id="billing_req_sent" name="billing_req_sent"
                                                   value="{{ old('billing_req_sent', $hb837->billing_req_sent ? $hb837->billing_req_sent->format('Y-m-d') : '') }}">
                                            @error('billing_req_sent')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="project_net_profit">Project Net Profit</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control @error('project_net_profit') is-invalid @enderror"
                                                       id="project_net_profit" name="project_net_profit" value="{{ old('project_net_profit', (string)$hb837->project_net_profit) }}">
                                                @error('project_net_profit')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="report_submitted">Report Submitted</label>
                                            <input type="date" class="form-control @error('report_submitted') is-invalid @enderror"
                                                   id="report_submitted" name="report_submitted"
                                                   value="{{ old('report_submitted', $hb837->report_submitted ? $hb837->report_submitted->format('Y-m-d') : '') }}">
                                            @error('report_submitted')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="agreement_submitted">Agreement Submitted</label>
                                            <input type="date" class="form-control @error('agreement_submitted') is-invalid @enderror"
                                                   id="agreement_submitted" name="agreement_submitted"
                                                   value="{{ old('agreement_submitted', $hb837->agreement_submitted ? $hb837->agreement_submitted->format('Y-m-d') : '') }}">
                                            @error('agreement_submitted')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="financial_notes">Financial Notes</label>
                                            <textarea class="form-control @error('financial_notes') is-invalid @enderror" id="financial_notes" name="financial_notes" rows="4">{{ old('financial_notes', $hb837->financial_notes) }}</textarea>
                                            @error('financial_notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Tab -->
                            <div class="tab-pane fade {{ $tab === 'address' ? 'show active' : '' }}" id="address" role="tabpanel" aria-labelledby="address-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-map-marker-alt text-primary"></i> Address & Location
                                    </h4>
                                    <p class="text-muted">Property address and location details</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address_tab">Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                                   id="address_tab" name="address" value="{{ old('address', $hb837->address) }}" required>
                                            @error('address')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="city_tab">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                   id="city_tab" name="city" value="{{ old('city', $hb837->city) }}">
                                            @error('city')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="county_tab">County</label>
                                            <input type="text" class="form-control @error('county') is-invalid @enderror"
                                                   id="county_tab" name="county" value="{{ old('county', $hb837->county) }}">
                                            @error('county')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="state_tab">State</label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror"
                                                   id="state_tab" name="state" value="{{ old('state', $hb837->state) }}" maxlength="2">
                                            @error('state')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="zip_tab">ZIP</label>
                                            <input type="text" class="form-control @error('zip') is-invalid @enderror"
                                                   id="zip_tab" name="zip" value="{{ old('zip', $hb837->zip) }}">
                                            @error('zip')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="phone_tab">Phone</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                   id="phone_tab" name="phone" value="{{ old('phone', $hb837->phone) }}">
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div class="tab-pane fade {{ $tab === 'notes' ? 'show active' : '' }}" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-sticky-note text-primary"></i> Notes & Comments
                                    </h4>
                                    <p class="text-muted">General and financial notes for this project</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes_tab">General Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes_tab" name="notes" rows="6">{{ old('notes', $hb837->notes) }}</textarea>
                                            @error('notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="financial_notes_tab">Financial Notes</label>
                                            <textarea class="form-control @error('financial_notes') is-invalid @enderror" id="financial_notes_tab" name="financial_notes" rows="4">{{ old('financial_notes', $hb837->financial_notes) }}</textarea>
                                            @error('financial_notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Files Tab -->
                            <div class="tab-pane fade {{ $tab === 'files' ? 'show active' : '' }}" id="files" role="tabpanel" aria-labelledby="files-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-file-alt text-primary"></i> File Management
                                    </h4>
                                    <p class="text-muted">Upload, download, and manage project files</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">File Management</h5>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                                                        <i class="fas fa-upload"></i> Upload File
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if($hb837->files && $hb837->files->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>File Name</th>
                                                                    <th>Category</th>
                                                                    <th>Size</th>
                                                                    <th>Uploaded</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($hb837->files as $file)
                                                                    <tr>
                                                                        <td>{{ $file->original_filename }}</td>
                                                                        <td>{{ $file->file_category ?? 'General' }}</td>
                                                                        <td>{{ $file->file_size_human }}</td>
                                                                        <td>{{ $file->created_at->format('M j, Y') }}</td>
                                                                        <td>
                                                                            <a href="{{ $file->download_url }}" class="btn btn-sm btn-info">
                                                                                <i class="fas fa-download"></i> Download
                                                                            </a>
                                                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteFile({{ $file->id }})">
                                                                                <i class="fas fa-trash"></i> Delete
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">No files uploaded yet.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Maps Tab -->
                            <div class="tab-pane fade {{ $tab === 'maps' ? 'show active' : '' }}" id="maps" role="tabpanel" aria-labelledby="maps-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-map text-primary"></i> Location & Maps
                                    </h4>
                                    <p class="text-muted">Property location, mapping, and security information</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Property Location & Maps</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Address Information</h6>
                                                        <address>
                                                            <strong>{{ $hb837->property_name }}</strong><br>
                                                            {{ $hb837->address }}<br>
                                                            {{ $hb837->city }}, {{ $hb837->state }} {{ $hb837->zip }}
                                                        </address>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Security Gauge</h6>
                                                        <p><strong>Crime Risk:</strong>
                                                            <span class="badge badge-{{ $hb837->securitygauge_crime_risk === 'Elevated' ? 'warning' : ($hb837->securitygauge_crime_risk === 'High' ? 'danger' : 'success') }}">
                                                                {{ $hb837->securitygauge_crime_risk ?? 'Unknown' }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <h6 class="mb-0">Property Location Map</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="alert alert-info">
                                                                    <i class="fas fa-map-marker-alt"></i>
                                                                    <strong>Address:</strong> {{ $hb837->address }}, {{ $hb837->city }}, {{ $hb837->state }} {{ $hb837->zip }}
                                                                </div>
                                                                <div class="mt-2">
                                                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($hb837->address . ', ' . $hb837->city . ', ' . $hb837->state) }}"
                                                                       target="_blank" class="btn btn-primary btn-sm">
                                                                        <i class="fas fa-external-link-alt"></i> Open in Google Maps
                                                                    </a>
                                                                    <a href="https://www.openstreetmap.org/search?query={{ urlencode($hb837->address . ', ' . $hb837->city . ', ' . $hb837->state) }}"
                                                                       target="_blank" class="btn btn-outline-secondary btn-sm ml-2">
                                                                        <i class="fas fa-map"></i> View on OpenStreetMap
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Fields Tab -->
                            <div class="tab-pane fade {{ $tab === 'custom' ? 'show active' : '' }}" id="custom" role="tabpanel" aria-labelledby="custom-tab">
                                <div class="tab-header">
                                    <h4 class="tab-title">
                                        <i class="fas fa-wrench text-primary"></i> Custom Fields
                                    </h4>
                                    <p class="text-muted">Manage custom fields for this property</p>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        @php
                                            $customFields = \App\Models\HB837ImportFieldConfig::customFields()->active()->get();
                                        @endphp

                                        @if($customFields->count() > 0)
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">
                                                        <i class="fas fa-cogs"></i> Custom Field Values
                                                    </h5>
                                                    <div class="card-tools">
                                                        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-cog"></i> Manage Custom Fields
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach($customFields as $field)
                                                            <div class="col-md-6 mb-3">
                                                                <div class="form-group">
                                                                    <label for="custom_{{ $field->database_field }}">
                                                                        {{ $field->field_label }}
                                                                        @if($field->is_required_for_import)
                                                                            <span class="text-danger">*</span>
                                                                        @endif
                                                                    </label>
                                                                    @if($field->description)
                                                                        <small class="form-text text-muted">{{ $field->description }}</small>
                                                                    @endif

                                                                    @switch($field->field_type)
                                                                        @case('text')
                                                                        @case('string')
                                                                            <input type="text"
                                                                                   class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                   id="custom_{{ $field->database_field }}"
                                                                                   name="custom_{{ $field->database_field }}"
                                                                                   value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                                                   @if($field->max_length) maxlength="{{ $field->max_length }}" @endif
                                                                                   @if($field->is_required_for_import) required @endif>
                                                                            @break

                                                                        @case('textarea')
                                                                            <textarea class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                      id="custom_{{ $field->database_field }}"
                                                                                      name="custom_{{ $field->database_field }}"
                                                                                      rows="3"
                                                                                      @if($field->max_length) maxlength="{{ $field->max_length }}" @endif
                                                                                      @if($field->is_required_for_import) required @endif>{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}</textarea>
                                                                            @break

                                                                        @case('number')
                                                                        @case('integer')
                                                                            <input type="number"
                                                                                   class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                   id="custom_{{ $field->database_field }}"
                                                                                   name="custom_{{ $field->database_field }}"
                                                                                   value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                                                   @if($field->is_required_for_import) required @endif>
                                                                            @break

                                                                        @case('decimal')
                                                                        @case('float')
                                                                            <input type="number"
                                                                                   step="0.01"
                                                                                   class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                   id="custom_{{ $field->database_field }}"
                                                                                   name="custom_{{ $field->database_field }}"
                                                                                   value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                                                   @if($field->is_required_for_import) required @endif>
                                                                            @break

                                                                        @case('date')
                                                                            <input type="date"
                                                                                   class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                   id="custom_{{ $field->database_field }}"
                                                                                   name="custom_{{ $field->database_field }}"
                                                                                   value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                                                   @if($field->is_required_for_import) required @endif>
                                                                            @break

                                                                        @case('enum')
                                                                            @if($field->enum_values)
                                                                                <select class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                        id="custom_{{ $field->database_field }}"
                                                                                        name="custom_{{ $field->database_field }}"
                                                                                        @if($field->is_required_for_import) required @endif>
                                                                                    @if(!$field->is_required_for_import)
                                                                                        <option value="">-- Select {{ $field->field_label }} --</option>
                                                                                    @endif
                                                                                    @foreach($field->enum_values as $value)
                                                                                        <option value="{{ $value }}"
                                                                                                {{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') == $value ? 'selected' : '' }}>
                                                                                            {{ ucfirst($value) }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @else
                                                                                <input type="text"
                                                                                       class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                       id="custom_{{ $field->database_field }}"
                                                                                       name="custom_{{ $field->database_field }}"
                                                                                       value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                                                       @if($field->is_required_for_import) required @endif>
                                                                            @endif
                                                                            @break

                                                                        @default
                                                                            <input type="text"
                                                                                   class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                                                   id="custom_{{ $field->database_field }}"
                                                                                   name="custom_{{ $field->database_field }}"
                                                                                   value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                                                   @if($field->max_length) maxlength="{{ $field->max_length }}" @endif
                                                                                   @if($field->is_required_for_import) required @endif>
                                                                    @endswitch

                                                                    @error('custom_'.$field->database_field)
                                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    <div class="py-4">
                                                        <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                                                        <h5 class="text-muted">No Custom Fields</h5>
                                                        <p class="text-muted">No custom fields have been configured yet.</p>
                                                        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-primary">
                                                            <i class="fas fa-plus"></i> Create Custom Fields
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Update Record
                            </button>
                            <a href="{{ route('admin.hb837.show', $hb837->id) }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="fileUploadForm" action="{{ route('admin.hb837.files.upload', $hb837->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="file">Select File</label>
                        <input type="file" class="form-control-file" id="file" name="file" required>
                    </div>
                    <div class="form-group">
                        <label for="file_category">Category</label>
                        <select class="form-control" id="file_category" name="file_category">
                            <option value="general">General</option>
                            <option value="report">Report</option>
                            <option value="contract">Contract</option>
                            <option value="assessment">Assessment</option>
                            <option value="correspondence">Correspondence</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="fileUploadForm" class="btn btn-primary">Upload File</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
$(document).ready(function() {
    const storageKey = 'hb837_edit_active_tab_{{ $hb837->id }}';

    // Initialize Bootstrap tabs
    $('#editTabs a[data-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Handle tab switching with URL updates and localStorage
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");
        const tabName = target.substring(1); // Remove the # symbol

        // Save to localStorage
        localStorage.setItem(storageKey, tabName);

        // Update URL without page reload
        const baseUrl = "{{ route('admin.hb837.edit', $hb837->id) }}";
        const newUrl = baseUrl + (tabName !== 'general' ? '/' + tabName : '');
        window.history.pushState({path: newUrl}, '', newUrl);

        console.log('Tab switched to:', tabName);
    });

    // Determine which tab to activate
    function getActiveTab() {
        // Priority: URL parameter > localStorage > default (general)
        const urlTab = "{{ $tab }}";
        const savedTab = localStorage.getItem(storageKey);

        console.log('URL tab:', urlTab);
        console.log('Saved tab:', savedTab);

        // If URL has a specific tab, use it
        if (urlTab && urlTab !== 'general') {
            return urlTab;
        }

        // If no URL tab but we have a saved tab, use it
        if (savedTab && savedTab !== 'general') {
            return savedTab;
        }

        // Default to general
        return 'general';
    }

    // Set active tab based on priority logic
    const activeTab = getActiveTab();
    console.log('Activating tab:', activeTab);

    if (activeTab && activeTab !== 'general') {
        // Use Bootstrap's tab method to properly show the tab
        const tabLink = '#' + activeTab + '-tab';
        console.log('Trying to activate tab link:', tabLink);
        if ($(tabLink).length > 0) {
            $(tabLink).tab('show');
            console.log('Tab activated:', tabLink);
        } else {
            console.error('Tab link not found:', tabLink);
            // Fallback to general tab
            $('#general-tab').tab('show');
        }
    } else {
        // Show general tab by default
        $('#general-tab').tab('show');
        console.log('Default general tab activated');
    }

    // File upload modal functionality
    $('#uploadModal').on('show.bs.modal', function (event) {
        // Modal setup code here
    });

    // Delete file function
    window.deleteFile = function(fileId) {
        if (confirm('Are you sure you want to delete this file?')) {
            $.ajax({
                url: '/admin/hb837/files/' + fileId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('File deleted successfully');
                    } else {
                        alert('File deleted successfully');
                    }
                    location.reload();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Unknown error';
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Error deleting file: ' + message);
                    } else {
                        alert('Error deleting file: ' + message);
                    }
                }
            });
        }
    };

    // Enhanced form validation and submission
    $('#hb837-edit-form').on('submit', function(e) {
        const requiredFields = ['property_name', 'address_general'];
        let isValid = true;

        requiredFields.forEach(function(field) {
            const $field = $('#' + field);
            if (!$field.val().trim()) {
                $field.addClass('is-invalid');
                isValid = false;
            } else {
                $field.removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please fill in all required fields');
            } else {
                alert('Please fill in all required fields');
            }
            return false;
        }

        // Store current tab before form submission
        const currentActiveTab = $('.nav-link.active').attr('href');
        if (currentActiveTab) {
            const tabName = currentActiveTab.substring(1);
            localStorage.setItem(storageKey, tabName);
            console.log('Stored tab before form submission:', tabName);
        }
    });

    // Clear localStorage when navigating away from edit page
    window.addEventListener('beforeunload', function() {
        // Only clear if we're navigating to a different page (not submitting form)
        if (!$('#hb837-edit-form').data('submitting')) {
            // Don't clear - keep the tab preference
            // localStorage.removeItem(storageKey);
        }
    });

    // Mark form as submitting to avoid clearing localStorage
    $('#hb837-edit-form').on('submit', function() {
        $(this).data('submitting', true);
    });
});
</script>
@stop
