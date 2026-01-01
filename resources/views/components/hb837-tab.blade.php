@php
    $tabFields = config("hb837.tab_fields.{$tab}", []);
    $fieldsPerColumn = ceil(count($tabFields) / 2);
    $leftFields = array_slice($tabFields, 0, $fieldsPerColumn);
    $rightFields = array_slice($tabFields, $fieldsPerColumn);
@endphp

{{-- Dynamic Tab Content --}}
<div class="row mt-3">
    <div class="col-md-6">
        @foreach($leftFields as $field)
            <x-hb837-form-field :field="$field" :record="$hb837" />
        @endforeach
    </div>

    <div class="col-md-6">
        @foreach($rightFields as $field)
            <x-hb837-form-field :field="$field" :record="$hb837" />
        @endforeach
    </div>
</div>
