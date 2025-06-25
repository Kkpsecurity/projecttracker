<form action="{{ isset($owner) ? route('admin.owners.update', $owner->id) : route('admin.owners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($owner))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name">Owner Name</label>
        <input type="text" name="name" class="form-control" id="name"
               value="{{ old('name', $owner->name ?? '') }}"
               placeholder="Enter owner name">
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Owner Email</label>
        <input type="email" name="email" class="form-control" id="email"
               value="{{ old('email', $owner->email ?? '') }}"
               placeholder="Enter email">
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" name="phone" class="form-control" id="phone"
               value="{{ old('phone', $owner->phone ?? '') }}"
               placeholder="Enter phone number">
        @error('phone')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="address">Address</label>
        <input type="text" name="address" class="form-control" id="address"
               value="{{ old('address', $owner->address ?? '') }}"
               placeholder="Enter address">
        @error('address')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="city">City</label>
            <input type="text" name="city" class="form-control" id="city"
                   value="{{ old('city', $owner->city ?? '') }}"
                   placeholder="City">
            @error('city')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="state">State</label>
            <input type="text" name="state" class="form-control" id="state"
                   value="{{ old('state', $owner->state ?? '') }}"
                   placeholder="State" maxlength="2">
            @error('state')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="zip">Zip Code</label>
            <input type="text" name="zip" class="form-control" id="zip"
                   value="{{ old('zip', $owner->zip ?? '') }}"
                   placeholder="Zip Code">
            @error('zip')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="company_name">Company Name</label>
        <input type="text" name="company_name" class="form-control" id="company_name"
               value="{{ old('company_name', $owner->company_name ?? '') }}"
               placeholder="Enter company name">
        @error('company_name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label for="tax_id">Tax ID</label>
        <input type="text" name="tax_id" class="form-control" id="tax_id"
               value="{{ old('tax_id', $owner->tax_id ?? '') }}"
               placeholder="Enter tax ID">
        @error('tax_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
