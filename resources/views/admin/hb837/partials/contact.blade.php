{{-- Contact Information Tab Content --}}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="property_manager_name">Property Manager Name</label>
            <input type="text" class="form-control @error('property_manager_name') is-invalid @enderror" 
                   id="property_manager_name" name="property_manager_name" 
                   value="{{ old('property_manager_name', $hb837->property_manager_name ?? '') }}" 
                   placeholder="Property manager name">
            @error('property_manager_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="property_manager_email">Property Manager Email</label>
            <input type="email" class="form-control @error('property_manager_email') is-invalid @enderror" 
                   id="property_manager_email" name="property_manager_email" 
                   value="{{ old('property_manager_email', $hb837->property_manager_email ?? '') }}" 
                   placeholder="manager@company.com">
            @error('property_manager_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" name="phone" 
                   value="{{ old('phone', $hb837->phone ?? '') }}" 
                   placeholder="(555) 123-4567">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="regional_manager_name">Regional Manager Name</label>
            <input type="text" class="form-control @error('regional_manager_name') is-invalid @enderror" 
                   id="regional_manager_name" name="regional_manager_name" 
                   value="{{ old('regional_manager_name', $hb837->regional_manager_name ?? '') }}" 
                   placeholder="Regional manager name">
            @error('regional_manager_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="regional_manager_email">Regional Manager Email</label>
            <input type="email" class="form-control @error('regional_manager_email') is-invalid @enderror" 
                   id="regional_manager_email" name="regional_manager_email" 
                   value="{{ old('regional_manager_email', $hb837->regional_manager_email ?? '') }}" 
                   placeholder="regional@company.com">
            @error('regional_manager_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="macro_client">Macro Client</label>
            <input type="text" class="form-control @error('macro_client') is-invalid @enderror" 
                   id="macro_client" name="macro_client" 
                   value="{{ old('macro_client', $hb837->macro_client ?? '') }}" 
                   placeholder="Macro client name">
            @error('macro_client')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
