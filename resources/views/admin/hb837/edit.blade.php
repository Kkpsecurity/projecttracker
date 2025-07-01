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
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">
                                    <i class="fas fa-home"></i> General
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="contacts-tab" data-toggle="tab" href="#contacts" role="tab" aria-controls="contacts" aria-selected="false">
                                    <i class="fas fa-users"></i> Contacts
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="financial-tab" data-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="false">
                                    <i class="fas fa-dollar-sign"></i> Financial
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content" id="editTabsContent">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
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
                                            <label for="address">Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                                   id="address" name="address" value="{{ old('address', $hb837->address) }}" required>
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
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes', $hb837->notes) }}</textarea>
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
                            <div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
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
                            <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
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
@stop
