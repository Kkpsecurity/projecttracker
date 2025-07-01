@extends('adminlte::page')

@section('title', 'Edit Consultant - ProjectTracker Fresh')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit Consultant</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.consultants.index') }}">Consultants</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.consultants.show', $consultant->id) }}">{{ $consultant->full_name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
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
                    <h3 class="card-title">Edit: {{ $consultant->full_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.consultants.show', $consultant->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('admin.consultants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.consultants.update', $consultant->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Personal Information</h5>

                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name" name="first_name" value="{{ old('first_name', $consultant->first_name) }}" required>
                                    @error('first_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name" name="last_name" value="{{ old('last_name', $consultant->last_name) }}" required>
                                    @error('last_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $consultant->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="dba_company_name">Company/DBA Name</label>
                                    <input type="text" class="form-control @error('dba_company_name') is-invalid @enderror"
                                           id="dba_company_name" name="dba_company_name" value="{{ old('dba_company_name', $consultant->dba_company_name) }}">
                                    @error('dba_company_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="mailing_address">Mailing Address</label>
                                    <textarea class="form-control @error('mailing_address') is-invalid @enderror"
                                              id="mailing_address" name="mailing_address" rows="3">{{ old('mailing_address', $consultant->mailing_address) }}</textarea>
                                    @error('mailing_address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Professional Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Professional Information</h5>

                                <div class="form-group">
                                    <label for="fcp_expiration_date">FCP Expiration Date</label>
                                    <input type="date" class="form-control @error('fcp_expiration_date') is-invalid @enderror"
                                           id="fcp_expiration_date" name="fcp_expiration_date"
                                           value="{{ old('fcp_expiration_date', $consultant->fcp_expiration_date ? $consultant->fcp_expiration_date->format('Y-m-d') : '') }}">
                                    @error('fcp_expiration_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="assigned_light_meter">Assigned Light Meter</label>
                                    <input type="text" class="form-control @error('assigned_light_meter') is-invalid @enderror"
                                           id="assigned_light_meter" name="assigned_light_meter" value="{{ old('assigned_light_meter', $consultant->assigned_light_meter) }}">
                                    @error('assigned_light_meter')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="lm_nist_expiration_date">Light Meter NIST Expiration Date</label>
                                    <input type="date" class="form-control @error('lm_nist_expiration_date') is-invalid @enderror"
                                           id="lm_nist_expiration_date" name="lm_nist_expiration_date"
                                           value="{{ old('lm_nist_expiration_date', $consultant->lm_nist_expiration_date ? $consultant->lm_nist_expiration_date->format('Y-m-d') : '') }}">
                                    @error('lm_nist_expiration_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="subcontractor_bonus_rate">Subcontractor Bonus Rate</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('subcontractor_bonus_rate') is-invalid @enderror"
                                               id="subcontractor_bonus_rate" name="subcontractor_bonus_rate"
                                               value="{{ old('subcontractor_bonus_rate', (string)$consultant->subcontractor_bonus_rate) }}" min="0">
                                        @error('subcontractor_bonus_rate')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="4">{{ old('notes', $consultant->notes) }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Update Consultant
                            </button>
                            <a href="{{ route('admin.consultants.show', $consultant->id) }}" class="btn btn-secondary btn-lg ml-2">
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
