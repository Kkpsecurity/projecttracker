<div class="row">
    <div class="col-md-12">
        <!-- Notes -->
        <div class="form-group mt-3">
            <label for="notes">Notes</label>
            <textarea
                class="form-control"
                id="notes"
                name="notes"
                rows="8">{{ old('notes', $hb837->notes) }}</textarea>
            @error('notes')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- Consultant Notes -->
        <div class="form-group mt-3">
            <label for="consultant_notes">Notes to Consultant</label>
            <textarea
                class="form-control"
                id="consultant_notes"
                name="consultant_notes"
                rows="8">{{ old('consultant_notes', $hb837->consultant_notes) }}</textarea>
            @error('consultant_notes')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
