@extends('adminlte::page')

@section('title', 'Create HB837 Record - ProjectTracker')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-plus"></i> Create HB837 Record</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <form action="{{ route('admin.hb837.store') }}" method="POST" id="hb837-form">
                @csrf
                
                <!-- General Information Card -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> General Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_name">Property Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('property_name') is-invalid @enderror" 
                                           id="property_name" name="property_name" value="{{ old('property_name') }}" required>
                                    @error('property_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="management_company">Management Company</label>
                                    <input type="text" class="form-control @error('management_company') is-invalid @enderror" 
                                           id="management_company" name="management_company" value="{{ old('management_company') }}">
                                    @error('management_company')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_name">Owner Name</label>
                                    <input type="text" class="form-control @error('owner_name') is-invalid @enderror" 
                                           id="owner_name" name="owner_name" value="{{ old('owner_name') }}">
                                    @error('owner_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="property_type">Property Type</label>
                                    <select class="form-control @error('property_type') is-invalid @enderror" 
                                            id="property_type" name="property_type">
                                        <option value="">Select Type</option>
                                        @if(isset($propertyTypes))
                                            @foreach($propertyTypes as $type)
                                                <option value="{{ $type }}" {{ old('property_type') == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('property_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="units">Units</label>
                                    <input type="number" class="form-control @error('units') is-invalid @enderror" 
                                           id="units" name="units" value="{{ old('units') }}" min="1">
                                    @error('units')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information Card -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Address Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address') }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="county">County</label>
                                    <input type="text" class="form-control @error('county') is-invalid @enderror" 
                                           id="county" name="county" value="{{ old('county') }}">
                                    @error('county')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                           id="state" name="state" value="{{ old('state') }}" maxlength="2">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="zip">ZIP Code</label>
                                    <input type="text" class="form-control @error('zip') is-invalid @enderror" 
                                           id="zip" name="zip" value="{{ old('zip') }}">
                                    @error('zip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignment & Status Card -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-tie"></i> Assignment & Status</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="assigned_consultant_id">Assigned Consultant</label>
                                    <select class="form-control @error('assigned_consultant_id') is-invalid @enderror" 
                                            id="assigned_consultant_id" name="assigned_consultant_id">
                                        <option value="">Unassigned</option>
                                        @if(isset($consultants))
                                            @foreach($consultants as $consultant)
                                                <option value="{{ $consultant->id }}" {{ old('assigned_consultant_id') == $consultant->id ? 'selected' : '' }}>
                                                    {{ $consultant->first_name }} {{ $consultant->last_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('assigned_consultant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scheduled_date_of_inspection">Scheduled Inspection Date</label>
                                    <input type="date" class="form-control @error('scheduled_date_of_inspection') is-invalid @enderror" 
                                           id="scheduled_date_of_inspection" name="scheduled_date_of_inspection" 
                                           value="{{ old('scheduled_date_of_inspection') }}">
                                    @error('scheduled_date_of_inspection')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="report_status">Report Status</label>
                                    <select class="form-control @error('report_status') is-invalid @enderror" 
                                            id="report_status" name="report_status">
                                        <option value="">Select Status</option>
                                        <option value="not-started" {{ old('report_status') == 'not-started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="in-progress" {{ old('report_status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="in-review" {{ old('report_status') == 'in-review' ? 'selected' : '' }}>In Review</option>
                                        <option value="completed" {{ old('report_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('report_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contracting_status">Contracting Status</label>
                                    <select class="form-control @error('contracting_status') is-invalid @enderror" 
                                            id="contracting_status" name="contracting_status">
                                        <option value="">Select Status</option>
                                        <option value="quoted" {{ old('contracting_status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                                        <option value="started" {{ old('contracting_status') == 'started' ? 'selected' : '' }}>Started</option>
                                        <option value="executed" {{ old('contracting_status') == 'executed' ? 'selected' : '' }}>Executed</option>
                                        <option value="closed" {{ old('contracting_status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    @error('contracting_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="securitygauge_crime_risk">Security Gauge Crime Risk</label>
                                    <select class="form-control @error('securitygauge_crime_risk') is-invalid @enderror" 
                                            id="securitygauge_crime_risk" name="securitygauge_crime_risk">
                                        <option value="">Not Assessed</option>
                                        @foreach(config('hb837.security_gauge', []) as $key => $value)
                                            <option value="{{ $value }}" {{ old('securitygauge_crime_risk') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('securitygauge_crime_risk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Information Card -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Financial Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quoted_price">Quoted Price</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('quoted_price') is-invalid @enderror" 
                                               id="quoted_price" name="quoted_price" value="{{ old('quoted_price') }}" 
                                               step="0.01" min="0">
                                        @error('quoted_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sub_fees_estimated_expenses">Sub Fees / Estimated Expenses</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('sub_fees_estimated_expenses') is-invalid @enderror" 
                                               id="sub_fees_estimated_expenses" name="sub_fees_estimated_expenses" 
                                               value="{{ old('sub_fees_estimated_expenses') }}" step="0.01" min="0">
                                        @error('sub_fees_estimated_expenses')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Information Card -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-building"></i> Client Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="macro_client">Macro Client</label>
                                    <input type="text" class="form-control @error('macro_client') is-invalid @enderror" 
                                           id="macro_client" name="macro_client" value="{{ old('macro_client') }}">
                                    @error('macro_client')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="macro_contact">Macro Contact</label>
                                    <input type="text" class="form-control @error('macro_contact') is-invalid @enderror" 
                                           id="macro_contact" name="macro_contact" value="{{ old('macro_contact') }}">
                                    @error('macro_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="macro_email">Macro Email</label>
                                    <input type="email" class="form-control @error('macro_email') is-invalid @enderror" 
                                           id="macro_email" name="macro_email" value="{{ old('macro_email') }}">
                                    @error('macro_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Create HB837 Record
                                </button>
                                <a href="{{ route('admin.hb837.index') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.card-header {
    font-weight: bold;
}

.form-group label {
    font-weight: 600;
    color: #333;
}

.text-danger {
    font-weight: bold;
}

.input-group-text {
    font-weight: bold;
}

/* Color coding preview for select options */
option[value="Low"] { background-color: #72b862; color: white; }
option[value="Moderate"] { background-color: #95f181; color: black; }
option[value="Elevated"] { background-color: #fae099; color: black; }
option[value="High"] { background-color: #f2a36e; color: black; }
option[value="Severe"] { background-color: #c75845; color: white; }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Auto-calculate project net profit
    function calculateNetProfit() {
        let quoted = parseFloat($('#quoted_price').val()) || 0;
        let expenses = parseFloat($('#sub_fees_estimated_expenses').val()) || 0;
        let profit = quoted - expenses;
        
        // Update the net profit field
        $('#project_net_profit').val(profit.toFixed(2));
        
        // Add visual feedback
        const $profitField = $('#project_net_profit');
        $profitField.removeClass('text-success text-danger text-warning');
        
        if (quoted > 0 && expenses > 0) {
            if (profit > 0) {
                $profitField.addClass('text-success');
            } else if (profit < 0) {
                $profitField.addClass('text-danger');
            } else {
                $profitField.addClass('text-warning');
            }
        }
    }
    
    // Bind calculation to input events
    $('#quoted_price, #sub_fees_estimated_expenses').on('input change', calculateNetProfit);
    
    // Calculate on page load if values exist
    calculateNetProfit();
    
    // Form validation
    $('#hb837-form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        if (!$('#property_name').val()) {
            isValid = false;
            $('#property_name').addClass('is-invalid');
        }
        
        if (!$('#address').val()) {
            isValid = false;
            $('#address').addClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    
    // Remove validation error on input
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@stop
