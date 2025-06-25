<section style="min-height: 450px; height: auto;">
    <div class="form-group mt-3">
        <label for="owner_name">Owner Name</label>
        <input type="text" name="owner_name" id="owner_name" class="form-control"
            value="{{ old('owner_name', $hb837->owner_name) }}"
            placeholder="Enter owner's name" autocomplete="name">
        @error('owner_name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="property_manager_name">Property Manager Name</label>
        <input type="text" name="property_manager_name" id="property_manager_name" class="form-control"
            value="{{ old('property_manager_name', $hb837->property_manager_name) }}"
            placeholder="Enter property manager's name" autocomplete="name">
        @error('property_manager_name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="property_manager_email">Property Manager Email</label>
        <div class="input-group">
            <input type="email" name="property_manager_email" id="property_manager_email" class="form-control"
                value="{{ old('property_manager_email', $hb837->property_manager_email) }}"
                placeholder="Enter property manager's email" autocomplete="email">
            <button type="button" class="btn btn-outline-secondary"
                onclick="window.location.href='mailto:{{ $hb837->property_manager_email }}'">
                <i class="fa fa-envelope"></i>
            </button>
        </div>
        @error('property_manager_email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="regional_manager_name">Regional Manager Name</label>
        <input type="text" name="regional_manager_name" id="regional_manager_name" class="form-control"
            value="{{ old('regional_manager_name', $hb837->regional_manager_name) }}"
            placeholder="Enter regional manager's name" autocomplete="name">
        @error('regional_manager_name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>


    <div class="form-group mt-3">
        <label for="regional_manager_email">Regional Manager Email</label>
        <div class="input-group">
            <input type="email" name="regional_manager_email" id="regional_manager_email" class="form-control"
                value="{{ old('regional_manager_email', $hb837->regional_manager_email) }}"
                placeholder="Enter regional manager's email" autocomplete="email">
            <button type="button" class="btn btn-outline-secondary"
                onclick="window.location.href='mailto:{{ $hb837->regional_manager_email }}'">
                <i class="fa fa-envelope"></i>
            </button>
        </div>
        @error('regional_manager_email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control"
            value="{{ old('phone', $hb837->phone) }}"
            placeholder="Enter phone number" autocomplete="tel">
        @error('phone')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</section>
