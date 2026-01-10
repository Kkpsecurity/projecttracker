{{-- Address Information Tab Content --}}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="address">Address <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                   id="address" name="address" 
                   value="{{ old('address', $hb837->address ?? '') }}" 
                   placeholder="Enter full address" required>
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
                   id="city" name="city" 
                   value="{{ old('city', $hb837->city ?? '') }}" 
                   placeholder="City">
            @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="county">County</label>
            <input type="text" class="form-control @error('county') is-invalid @enderror" 
                   id="county" name="county" 
                   value="{{ old('county', $hb837->county ?? '') }}" 
                   placeholder="County">
            @error('county')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label for="state">State</label>
            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                   id="state" name="state" 
                   value="{{ old('state', $hb837->state ?? '') }}" 
                   placeholder="FL" maxlength="2">
            @error('state')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label for="zip">ZIP Code</label>
            <input type="text" class="form-control @error('zip') is-invalid @enderror" 
                   id="zip" name="zip" 
                   value="{{ old('zip', $hb837->zip ?? '') }}" 
                   placeholder="ZIP">
            @error('zip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
