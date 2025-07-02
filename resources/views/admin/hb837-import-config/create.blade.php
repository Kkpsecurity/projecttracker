@extends('adminlte::page')

@section('title', isset($hb837ImportConfig) ? 'Edit Field Configuration' : 'Create Field Configuration')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ isset($hb837ImportConfig) ? 'Edit' : 'Create' }} Field Configuration</h1>
        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Validation Errors</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ isset($hb837ImportConfig) ? route('admin.hb837-import-config.update', $hb837ImportConfig) : route('admin.hb837-import-config.store') }}">
        @csrf
        @if(isset($hb837ImportConfig))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Basic Information</h3>
                    </div>
                    <div class="card-body">
                        <!-- Database Field -->
                        <div class="form-group">
                            <label for="database_field" class="required">Database Field Name</label>
                            <input type="text"
                                   class="form-control @error('database_field') is-invalid @enderror"
                                   id="database_field"
                                   name="database_field"
                                   value="{{ old('database_field', $hb837ImportConfig->database_field ?? '') }}"
                                   {{ isset($hb837ImportConfig) ? 'readonly' : '' }}
                                   placeholder="e.g. property_manager_contact">
                            <small class="form-text text-muted">
                                Lowercase letters, numbers, and underscores only. Cannot be changed after creation.
                            </small>
                            @error('database_field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Label -->
                        <div class="form-group">
                            <label for="field_label" class="required">Field Label</label>
                            <input type="text"
                                   class="form-control @error('field_label') is-invalid @enderror"
                                   id="field_label"
                                   name="field_label"
                                   value="{{ old('field_label', $hb837ImportConfig->field_label ?? '') }}"
                                   placeholder="e.g. Property Manager Contact">
                            <small class="form-text text-muted">
                                Human-readable label for this field.
                            </small>
                            @error('field_label')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="Optional description of this field...">{{ old('description', $hb837ImportConfig->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Type -->
                        <div class="form-group">
                            <label for="field_type" class="required">Field Type</label>
                            <select class="form-control @error('field_type') is-invalid @enderror"
                                    id="field_type"
                                    name="field_type"
                                    {{ isset($hb837ImportConfig) && $hb837ImportConfig->is_system_field ? 'disabled' : '' }}>
                                <option value="">Select field type...</option>
                                @foreach($fieldTypes as $value => $label)
                                    <option value="{{ $value }}"
                                            {{ old('field_type', $hb837ImportConfig->field_type ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('field_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Field Type Specific Options -->
                        <div id="string-options" class="field-type-options" style="display: none;">
                            <div class="form-group">
                                <label for="max_length">Maximum Length</label>
                                <input type="number"
                                       class="form-control @error('max_length') is-invalid @enderror"
                                       id="max_length"
                                       name="max_length"
                                       value="{{ old('max_length', $hb837ImportConfig->max_length ?? '') }}"
                                       min="1"
                                       max="65535"
                                       placeholder="255">
                                <small class="form-text text-muted">Default: 255 characters</small>
                                @error('max_length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="enum-options" class="field-type-options" style="display: none;">
                            <div class="form-group">
                                <label for="enum_values">Enum Values</label>
                                <textarea class="form-control @error('enum_values') is-invalid @enderror"
                                          id="enum_values"
                                          name="enum_values"
                                          rows="3"
                                          placeholder="value1, value2, value3">{{ old('enum_values', isset($hb837ImportConfig->enum_values) ? implode(', ', $hb837ImportConfig->enum_values) : '') }}</textarea>
                                <small class="form-text text-muted">Comma-separated list of allowed values</small>
                                @error('enum_values')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="foreign-key-options" class="field-type-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="foreign_table">Foreign Table</label>
                                        <input type="text"
                                               class="form-control @error('foreign_table') is-invalid @enderror"
                                               id="foreign_table"
                                               name="foreign_table"
                                               value="{{ old('foreign_table', $hb837ImportConfig->foreign_table ?? '') }}"
                                               placeholder="e.g. users">
                                        @error('foreign_table')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="foreign_key_column">Foreign Key Column</label>
                                        <input type="text"
                                               class="form-control @error('foreign_key_column') is-invalid @enderror"
                                               id="foreign_key_column"
                                               name="foreign_key_column"
                                               value="{{ old('foreign_key_column', $hb837ImportConfig->foreign_key_column ?? '') }}"
                                               placeholder="e.g. id">
                                        @error('foreign_key_column')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Default Value -->
                        <div class="form-group">
                            <label for="default_value">Default Value</label>
                            <input type="text"
                                   class="form-control @error('default_value') is-invalid @enderror"
                                   id="default_value"
                                   name="default_value"
                                   value="{{ old('default_value', $hb837ImportConfig->default_value ?? '') }}"
                                   placeholder="Optional default value">
                            @error('default_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Excel Mapping -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Excel Column Mapping</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="excel_column_mappings" class="required">Excel Column Names</label>
                            <textarea class="form-control @error('excel_column_mappings') is-invalid @enderror"
                                      id="excel_column_mappings"
                                      name="excel_column_mappings"
                                      rows="5"
                                      placeholder="Property Manager Contact&#10;PM Contact&#10;Manager Contact&#10;Site Manager">{{ old('excel_column_mappings', isset($hb837ImportConfig->excel_column_mappings) ? implode("\n", $hb837ImportConfig->excel_column_mappings) : '') }}</textarea>
                            <small class="form-text text-muted">
                                Enter each possible Excel column name on a separate line. These are the column headers that this field can match.
                            </small>
                            @error('excel_column_mappings')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Field Options -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Field Options</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="nullable"
                                       name="nullable"
                                       {{ old('nullable', $hb837ImportConfig->nullable ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="nullable">Nullable</label>
                                <small class="form-text text-muted">Allow empty values</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_required_for_import"
                                       name="is_required_for_import"
                                       {{ old('is_required_for_import', $hb837ImportConfig->is_required_for_import ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_required_for_import">Required for Import</label>
                                <small class="form-text text-muted">Import will fail if this field is missing</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_updatable"
                                       name="is_updatable"
                                       {{ old('is_updatable', $hb837ImportConfig->is_updatable ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_updatable">Updatable</label>
                                <small class="form-text text-muted">Allow updates to existing records</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_creatable"
                                       name="is_creatable"
                                       {{ old('is_creatable', $hb837ImportConfig->is_creatable ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_creatable">Creatable</label>
                                <small class="form-text text-muted">Include when creating new records</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       {{ old('is_active', $hb837ImportConfig->is_active ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                                <small class="form-text text-muted">Include in import process</small>
                            </div>
                        </div>

                        @if(!isset($hb837ImportConfig))
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="create_database_column"
                                       name="create_database_column"
                                       {{ old('create_database_column', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="create_database_column">Create Database Column</label>
                                <small class="form-text text-muted">Automatically create the database column</small>
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number"
                                   class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order"
                                   name="sort_order"
                                   value="{{ old('sort_order', $hb837ImportConfig->sort_order ?? 0) }}"
                                   min="0"
                                   step="10">
                            <small class="form-text text-muted">Lower numbers appear first</small>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Transformation -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Transformation</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="transformation_type">Transformation Type</label>
                            <select class="form-control @error('transformation_type') is-invalid @enderror"
                                    id="transformation_type"
                                    name="transformation_type">
                                <option value="">No transformation</option>
                                @foreach($transformationTypes as $value => $label)
                                    <option value="{{ $value }}"
                                            {{ old('transformation_type', $hb837ImportConfig->transformation_type ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('transformation_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="transformation_options">Transformation Options</label>
                            <textarea class="form-control @error('transformation_options') is-invalid @enderror"
                                      id="transformation_options"
                                      name="transformation_options"
                                      rows="3"
                                      placeholder="key1: value1&#10;key2: value2">{{ old('transformation_options', isset($hb837ImportConfig->transformation_options) ? implode("\n", array_map(fn($k, $v) => "$k: $v", array_keys($hb837ImportConfig->transformation_options), $hb837ImportConfig->transformation_options)) : '') }}</textarea>
                            <small class="form-text text-muted">Key-value pairs, one per line</small>
                            @error('transformation_options')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="validation_rules">Validation Rules</label>
                            <input type="text"
                                   class="form-control @error('validation_rules') is-invalid @enderror"
                                   id="validation_rules"
                                   name="validation_rules"
                                   value="{{ old('validation_rules', isset($hb837ImportConfig->validation_rules) ? implode('|', $hb837ImportConfig->validation_rules) : '') }}"
                                   placeholder="e.g. required|email|max:255">
                            <small class="form-text text-muted">Laravel validation rules, separated by |</small>
                            @error('validation_rules')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ isset($hb837ImportConfig) ? 'Update' : 'Create' }} Field Configuration
                        </button>
                        <a href="{{ route('admin.hb837-import-config.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
<style>
    .required::after {
        content: ' *';
        color: red;
    }
    .field-type-options {
        border-left: 3px solid #007bff;
        padding-left: 15px;
        margin-left: 10px;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Show/hide field type specific options
    function toggleFieldTypeOptions() {
        var fieldType = $('#field_type').val();
        $('.field-type-options').hide();

        if (fieldType === 'string' || fieldType === 'text') {
            $('#string-options').show();
        } else if (fieldType === 'enum') {
            $('#enum-options').show();
        } else if (fieldType === 'foreign_key') {
            $('#foreign-key-options').show();
        }
    }

    $('#field_type').on('change', toggleFieldTypeOptions);

    // Initialize on page load
    toggleFieldTypeOptions();

    // Auto-generate database field name from label
    $('#field_label').on('input', function() {
        if (!$('#database_field').prop('readonly')) {
            var label = $(this).val();
            var dbField = label.toLowerCase()
                               .replace(/[^a-z0-9\s]/g, '')
                               .replace(/\s+/g, '_')
                               .replace(/^_+|_+$/g, '');
            $('#database_field').val(dbField);
        }
    });
});
</script>
@stop
