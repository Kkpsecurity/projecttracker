@php
    $fieldConfig = config("hb837.field_definitions.{$field}");
    // Format date value for date fields
    if (($fieldConfig['type'] ?? '') === 'date') {
        $rawValue = old($field, $record->{$field} ?? ($fieldConfig['default'] ?? ''));
        $value = $rawValue ? \Illuminate\Support\Carbon::parse($rawValue)->format('Y-m-d') : '';
    } else {
        $value = old($field, $record->{$field} ?? ($fieldConfig['default'] ?? ''));
    }
    $required = $fieldConfig['required'] ?? false;
    $attributes = $fieldConfig['attributes'] ?? [];
@endphp

<div class="form-group">
    <label for="{{ $field }}">
        {{ $fieldConfig['label'] ?? ucwords(str_replace('_', ' ', $field)) }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @switch($fieldConfig['type'] ?? 'text')
        @case('select')
            @php
                $optionsKey = $fieldConfig['options'];
                $options = [];

                // Handle different option sources
                if ($optionsKey === 'consultants') {
                    $consultants = \App\Models\Consultant::all()->sortBy('last_name');
                    $options = $consultants;
                } elseif ($optionsKey === 'security_gauge') {
                    $options = config('hb837.security_gauge');
                } else {
                    $options = config("hb837.{$optionsKey}", []);
                }
            @endphp
            <select class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}"
                @if ($required) required @endif>

                @if (isset($fieldConfig['placeholder']))
                    <option value="">{{ $fieldConfig['placeholder'] }}</option>
                @endif



                @if ($optionsKey === 'consultants')
                    @foreach ($options as $option)
                        <option value="{{ $option->id }}" {{ $value == $option->id ? 'selected' : '' }}>
                            {{ $option->first_name }} {{ $option->last_name }}
                        </option>
                    @endforeach
                @elseif($optionsKey === 'security_gauge')
                    @foreach ($options as $key => $label)
                        <option value="{{ $label }}" {{ $value == $label ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                @else
                    @foreach ($options as $option)
                        <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>
                            {{ ucwords(str_replace('-', ' ', $option)) }}
                        </option>
                    @endforeach
                @endif
            </select>
        @break

        @case('textarea')
            <textarea class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}"
                placeholder="{{ $fieldConfig['placeholder'] ?? '' }}" @if ($required) required @endif
                @foreach ($attributes as $attr => $attrValue)
                          {{ $attr }}="{{ $attrValue }}" @endforeach>{{ $value }}</textarea>
        @break

        @default
            @if (in_array($field, ['quoted_price', 'sub_fees_estimated_expenses', 'project_net_profit']))
                {{-- Currency fields with $ prefix --}}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="{{ $fieldConfig['type'] ?? 'text' }}" class="form-control @error($field) is-invalid @enderror"
                        id="{{ $field }}" name="{{ $field }}" value="{{ $value }}"
                        placeholder="{{ $fieldConfig['placeholder'] ?? '' }}" @if ($required) required @endif
                        @if ($field === 'project_net_profit') readonly style="background-color: #f8f9fa;" @endif
                        @foreach ($attributes as $attr => $attrValue)
                               {{ $attr }}="{{ $attrValue }}" @endforeach>
                    @error($field)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @if (isset($fieldConfig['help_text']))
                    <small class="form-text text-muted">
                        <i class="fas fa-calculator text-primary"></i>
                        {{ $fieldConfig['help_text'] }}
                    </small>
                @endif
            @else
                {{-- Regular input fields --}}
                <input type="{{ $fieldConfig['type'] ?? 'text' }}" class="form-control @error($field) is-invalid @enderror"
                    id="{{ $field }}" name="{{ $field }}" value="{{ $value }}"
                    placeholder="{{ $fieldConfig['placeholder'] ?? '' }}" @if ($required) required @endif
                    @if ($field === 'project_net_profit') readonly style="background-color: #f8f9fa;" @endif
                    @foreach ($attributes as $attr => $attrValue)
                           {{ $attr }}="{{ $attrValue }}" @endforeach>
            @endif
    @endswitch

    @if (!in_array($field, ['quoted_price', 'sub_fees_estimated_expenses', 'project_net_profit']))
        @error($field)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @endif

    {{-- Help text for all field types --}}
    @if (isset($fieldConfig['help_text']) &&
            !in_array($field, ['quoted_price', 'sub_fees_estimated_expenses', 'project_net_profit']))
        <small class="form-text text-muted">
            {{ $fieldConfig['help_text'] }}
        </small>
    @endif
</div>
