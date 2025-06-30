{{-- Notes Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" 
                      id="notes" name="notes" rows="8" 
                      placeholder="Enter any additional notes, comments, or important information about this property...">{{ old('notes', $hb837->notes ?? '') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Include inspection notes, client communications, special requirements, or any other relevant information.
            </small>
        </div>
    </div>
</div>
