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
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_name">Property Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('property_name') is-invalid @enderror" 
                                           id="property_name" name="property_name" value="{{ old('property_name', $hb837->property_name) }}" required>
                                    @error('property_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="management_company">Management Company</label>
                                    <input type="text" class="form-control @error('management_company') is-invalid @enderror" 
                                           id="management_company" name="management_company" value="{{ old('management_company', $hb837->management_company) }}">
                                    @error('management_company')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address', $hb837->address) }}" required>
                                    @error('address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $hb837->city) }}">
                                    @error('city')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="county">County</label>
                                    <input type="text" class="form-control @error('county') is-invalid @enderror" 
                                           id="county" name="county" value="{{ old('county', $hb837->county) }}">
                                    @error('county')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                           id="state" name="state" value="{{ old('state', $hb837->state) }}" maxlength="2">
                                    @error('state')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
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

                        <div class="row">
                            <div class="col-md-6">
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
                            </div>
                            <div class="col-md-6">
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

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes', $hb837->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group text-right">
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
