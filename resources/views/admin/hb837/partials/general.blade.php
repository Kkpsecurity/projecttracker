{{-- General Information Tab Content --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="property_name">Property Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('property_name') is-invalid @enderror" 
                   id="property_name" name="property_name" 
                   value="{{ old('property_name', $hb837->property_name ?? '') }}" 
                   placeholder="Enter property name" required>
            @error('property_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="property_type">Property Type</label>
            <select class="form-control @error('property_type') is-invalid @enderror" 
                    id="property_type" name="property_type">
                <option value="">Select property type</option>
                <option value="garden" {{ old('property_type', $hb837->property_type ?? '') == 'garden' ? 'selected' : '' }}>Garden</option>
                <option value="midrise" {{ old('property_type', $hb837->property_type ?? '') == 'midrise' ? 'selected' : '' }}>Midrise</option>
                <option value="highrise" {{ old('property_type', $hb837->property_type ?? '') == 'highrise' ? 'selected' : '' }}>Highrise</option>
            </select>
            @error('property_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="units">Number of Units</label>
            <input type="number" class="form-control @error('units') is-invalid @enderror" 
                   id="units" name="units" 
                   value="{{ old('units', $hb837->units ?? '') }}" 
                   placeholder="Number of units" min="1">
            @error('units')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="management_company">Management Company</label>
            <input type="text" class="form-control @error('management_company') is-invalid @enderror" 
                   id="management_company" name="management_company" 
                   value="{{ old('management_company', $hb837->management_company ?? '') }}" 
                   placeholder="Management company name">
            @error('management_company')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="assigned_consultant_id">Assigned Consultant</label>
            <select class="form-control @error('assigned_consultant_id') is-invalid @enderror" 
                    id="assigned_consultant_id" name="assigned_consultant_id">
                <option value="">Select consultant</option>
                @if(isset($consultants))
                    @foreach($consultants as $consultant)
                        <option value="{{ $consultant->id }}" 
                                {{ old('assigned_consultant_id', $hb837->assigned_consultant_id ?? '') == $consultant->id ? 'selected' : '' }}>
                            {{ $consultant->first_name }} {{ $consultant->last_name }}
                        </option>
                    @endforeach
                @endif
            </select>

            @error('assigned_consultant_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="report_status">Report Status</label>
            <select class="form-control @error('report_status') is-invalid @enderror" 
                    id="report_status" name="report_status">
                <option value="not-started" {{ old('report_status', $hb837->report_status ?? '') == 'not-started' ? 'selected' : '' }}>Not Started</option>
                <option value="underway" {{ old('report_status', $hb837->report_status ?? '') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                <option value="in-review" {{ old('report_status', $hb837->report_status ?? '') == 'in-review' ? 'selected' : '' }}>In Review</option>
                <option value="completed" {{ old('report_status', $hb837->report_status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            @error('report_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="contracting_status">Contracting Status</label>
            <select class="form-control @error('contracting_status') is-invalid @enderror" 
                    id="contracting_status" name="contracting_status">
                <option value="">Select status</option>
                <option value="quoted" {{ old('contracting_status', $hb837->contracting_status ?? '') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                <option value="started" {{ old('contracting_status', $hb837->contracting_status ?? '') == 'started' ? 'selected' : '' }}>Started</option>
                <option value="executed" {{ old('contracting_status', $hb837->contracting_status ?? '') == 'executed' ? 'selected' : '' }}>Executed</option>
                <option value="closed" {{ old('contracting_status', $hb837->contracting_status ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            @error('contracting_status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="securitygauge_crime_risk">Crime Risk Level</label>
            <select class="form-control @error('securitygauge_crime_risk') is-invalid @enderror" 
                    id="securitygauge_crime_risk" name="securitygauge_crime_risk">
                <option value="">Select crime risk</option>
                @foreach(config('hb837.security_gauge', []) as $key => $value)
                    <option value="{{ $value }}" {{ old('securitygauge_crime_risk', $hb837->securitygauge_crime_risk ?? '') == $value ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
            @error('securitygauge_crime_risk')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>