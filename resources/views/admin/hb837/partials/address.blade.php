<section style="min-height: 450px; height: auto;">
    <div class="form-group mt-3">
        <label for="address">Address</label>
        <input type="text" name="address" id="address" class="form-control"
               value="{{ old('address', $hb837->address) }}"
               placeholder="Enter your address" required autocomplete="street-address">
        @error('address')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="city">City</label>
        <input type="text" name="city" id="city" class="form-control"
               value="{{ old('city', $hb837->city) }}"
               placeholder="Enter your city" required autocomplete="address-level2">
        @error('city')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="state">State</label>
        <input type="text" name="state" id="state" class="form-control" maxlength="2"
               value="{{ old('state', $hb837->state) }}"
               placeholder="State (e.g., FL)" required autocomplete="address-level1">
        @error('state')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mt-3">
        <label for="zip">Zip Code</label>
        <input type="text" name="zip" id="zip" class="form-control" maxlength="10"
               value="{{ old('zip', $hb837->zip) }}"
               placeholder="Enter zip code" required autocomplete="postal-code">
        @error('zip')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="county">County</label>
        <input type="text" name="county" id="county" class="form-control"
               value="{{ old('county', $hb837->county) }}"
               placeholder="Enter your county" required autocomplete="address-level2">
        @error('county')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

</section>
