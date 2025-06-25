<div class="row">
    <!-- Left Column -->
    <div class="col-md-6">
        <!-- Property Name -->
        <div class="form-group mt-3">
            <label for="property_name">Property Name</label>
            <input type="text" name="property_name" id="property_name" class="form-control"
                value="{{ old('property_name', $hb837->property_name) }}" placeholder="Enter property name">
            @error('property_name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Property Type (required) -->
        <div class="form-group mt-3">
            <label for="property_type">Property Type</label>
            @php
                $property_types = config('hb837.property_types');
            @endphp
            <select name="property_type" id="property_type" class="form-control" required>
                <option value="">Select property type</option>
                @foreach ($property_types as $type)
                    <option value="{{ $type }}"
                        {{ old('property_type', $hb837->property_type) == $type ? 'selected' : '' }}>
                        {{ Str::ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            @error('property_type')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Units (changed to number input) -->
        <div class="form-group mt-3">
            <label for="units">Units</label>
            <input type="number" name="units" id="units" class="form-control"
                value="{{ old('units', $hb837->units) }}" placeholder="Enter number of units" min="0">
            @error('units')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Management Company -->
        <div class="form-group mt-3">
            <label for="management_company">Management Company</label>
            <input type="text" name="management_company" id="management_company" class="form-control"
                value="{{ old('management_company', $hb837->management_company) }}"
                placeholder="Enter management company">
            @error('management_company')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- SecurityGauge Crime Risk Dropdown -->
        <div class="form-group mt-3">
            <label for="securitygauge_crime_risk">SecurityGauge Crime Risk</label>
            @php
                $securitygauge_crime_risks = config('hb837.security_gauge');
            @endphp
            <select name="securitygauge_crime_risk" id="securitygauge_crime_risk" class="form-control">
                <option value="">Select a Crime Risk</option>
                @foreach ($securitygauge_crime_risks as $risk)
                    <option value="{{ $risk }}"
                        {{ old('securitygauge_crime_risk', $hb837->securitygauge_crime_risk) == $risk ? 'selected' : '' }}>
                        {{ Str::ucfirst($risk) }}
                    </option>
                @endforeach
            </select>
            @error('securitygauge_crime_risk')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Consultant Selection / Info -->
        <div class="form-group mt-3">
            @php
                $consultants = App\Models\Consultant::all();
            @endphp
            <label for="assigned_consultant_id">Select Consultant</label>
            <select name="assigned_consultant_id" id="assigned_consultant_id" class="form-control">
                <option value="-1">Select a Consultant</option>
                @foreach ($consultants as $consultant)
                    <option value="{{ $consultant->id }}"
                        {{ (old('assigned_consultant_id') ?? $hb837->assigned_consultant_id) == $consultant->id ? 'selected' : '' }}>
                        {{ $consultant->first_name }} {{ $consultant->last_name }}
                    </option>
                @endforeach
            </select>
            @error('assigned_consultant_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-6">
        <div class="form-group mt-3">
            <label for="scheduled_date_of_inspection">Date of Scheduled Inspection</label>
            <input type="date" name="scheduled_date_of_inspection" id="scheduled_inspection_date"
                class="form-control"
                value="{{ old('scheduled_date_of_inspection', $hb837->scheduled_date_of_inspection) }}">
            @error('scheduled_date_of_inspection')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Report Status (required) -->
        <div class="form-group mt-3">
            <label for="report_status">Report Status</label>
            @php
                $report_statuses = config('hb837.report_statuses');
            @endphp
            <select name="report_status" id="report_status" class="form-control" required>
                <option value="">Select report status</option>
                @foreach ($report_statuses as $status)
                    <option value="{{ $status }}"
                        {{ old('report_status', $hb837->report_status) == $status ? 'selected' : '' }}>
                        {{ Str::ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            @error('report_status')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <div>
                <label for="report_submitted" class="form-label">Report Submitted:</label>
                <input type="date" name="report_submitted" id="report_submitted" class="form-control"
                    value="{{ old('report_submitted',  $hb837->report_submitted) }}">
            </div>
        </div>

        <!-- Contracting Status (required) -->
        <div class="form-group mt-3">
            <label for="contracting_status">Contracting Status</label>
            @php
                $contracting_statuses = config('hb837.contracting_statuses');
            @endphp
            <select name="contracting_status" id="contracting_status" class="form-control" required>
                <option value="">Select contracting status</option>
                @foreach ($contracting_statuses as $status)
                    <option value="{{ $status }}"
                        {{ old('contracting_status', $hb837->contracting_status) == $status ? 'selected' : '' }}>
                        {{ Str::ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            @error('contracting_status')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>


        <div class="col-md-6">
            <div class="mb-3">
                <label for="agreement_submitted" class="form-label">Agreement Submitted:</label>
                <input type="date" name="agreement_submitted" id="agreement_submitted" class="form-control"
                    value="{{ old('agreement_submitted', isset($consultant->agreement_submitted) && $consultant->agreement_submitted ? $consultant->agreement_submitted->format('Y-m-d') : '') }}">
            </div>
        </div>

        <!-- Macro Client -->
        <div class="form-group mt-3">
            <label for="macro_client">Macro Client</label>
            <input type="text" name="macro_client" id="macro_client" class="form-control"
                value="{{ old('macro_client', $hb837->macro_client) }}" placeholder="Enter macro client">
            @error('macro_client')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Macro Contact -->
        <div class="form-group mt-3">
            <label for="macro_contact">Macro Contact</label>
            <input type="text" name="macro_contact" id="macro_contact" class="form-control"
                value="{{ old('macro_contact', $hb837->macro_contact) }}" placeholder="Enter macro contact">
            @error('macro_contact')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Macro Email -->
        <div class="form-group mt-3">
            <label for="macro_email">Macro Email</label>
            <input type="email" name="macro_email" id="macro_email" class="form-control"
                value="{{ old('macro_email', $hb837->macro_email) }}" placeholder="Enter macro email"
                autocomplete="email">
            @error('macro_email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
