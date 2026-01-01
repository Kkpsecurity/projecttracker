{{-- Custom Fields Tab Content --}}

<div class="row mt-3">
    <div class="col-md-12">
        @php
            $customFields = \App\Models\HB837ImportFieldConfig::customFields()->active()->get();
        @endphp

        @if($customFields->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-cogs"></i> Custom Field Values
                    </h5>
                    <div class="card-tools">
                        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog"></i> Manage Custom Fields
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($customFields as $field)
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="custom_{{ $field->database_field }}">
                                        {{ $field->field_label }}
                                        @if($field->is_required_for_import)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    @if($field->description)
                                        <small class="form-text text-muted">{{ $field->description }}</small>
                                    @endif

                                    @switch($field->field_type)
                                        @case('text')
                                        @case('string')
                                            <input type="text"
                                                class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                id="custom_{{ $field->database_field }}"
                                                name="custom_{{ $field->database_field }}"
                                                value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                @if($field->max_length) maxlength="{{ $field->max_length }}" @endif
                                                @if($field->is_required_for_import) required @endif>
                                            @break

                                        @case('textarea')
                                            <textarea class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                    id="custom_{{ $field->database_field }}"
                                                    name="custom_{{ $field->database_field }}"
                                                    rows="3"
                                                    @if($field->max_length) maxlength="{{ $field->max_length }}" @endif
                                                    @if($field->is_required_for_import) required @endif>{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}</textarea>
                                            @break

                                        @case('number')
                                        @case('integer')
                                            <input type="number"
                                                class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                id="custom_{{ $field->database_field }}"
                                                name="custom_{{ $field->database_field }}"
                                                value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                @if($field->is_required_for_import) required @endif>
                                            @break

                                        @case('decimal')
                                        @case('float')
                                            <input type="number"
                                                step="0.01"
                                                class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                id="custom_{{ $field->database_field }}"
                                                name="custom_{{ $field->database_field }}"
                                                value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                @if($field->is_required_for_import) required @endif>
                                            @break

                                        @case('date')
                                            <input type="date"
                                                class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                id="custom_{{ $field->database_field }}"
                                                name="custom_{{ $field->database_field }}"
                                                value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                @if($field->is_required_for_import) required @endif>
                                            @break

                                        @case('enum')
                                            @if($field->enum_values)
                                                <select class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                        id="custom_{{ $field->database_field }}"
                                                        name="custom_{{ $field->database_field }}"
                                                        @if($field->is_required_for_import) required @endif>
                                                    @if(!$field->is_required_for_import)
                                                        <option value="">-- Select {{ $field->field_label }} --</option>
                                                    @endif
                                                    @foreach($field->enum_values as $value)
                                                        <option value="{{ $value }}"
                                                                {{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') == $value ? 'selected' : '' }}>
                                                            {{ ucfirst($value) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text"
                                                    class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                    id="custom_{{ $field->database_field }}"
                                                    name="custom_{{ $field->database_field }}"
                                                    value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                    @if($field->is_required_for_import) required @endif>
                                            @endif
                                            @break

                                        @default
                                            <input type="text"
                                                class="form-control @error('custom_'.$field->database_field) is-invalid @enderror"
                                                id="custom_{{ $field->database_field }}"
                                                name="custom_{{ $field->database_field }}"
                                                value="{{ old('custom_'.$field->database_field, $hb837->{$field->database_field} ?? '') }}"
                                                @if($field->max_length) maxlength="{{ $field->max_length }}" @endif
                                                @if($field->is_required_for_import) required @endif>
                                    @endswitch

                                    @error('custom_'.$field->database_field)
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center">
                    <div class="py-4">
                        <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Custom Fields</h5>
                        <p class="text-muted">No custom fields have been configured yet.</p>
                        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Custom Fields
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
