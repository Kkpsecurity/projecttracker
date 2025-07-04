@php
    // Assuming your URL is like: /admin/hb837/{id}/edit/{tab}
    // This will fetch the active tab from the 5th segment.
    $active_tab = request()->segment(5) ?? 'general';
@endphp

<div class="tab-content bg-gray text-dark" id="editTabContent" style="min-height: 300px; background: #ececec;">
    @include('partials.messages')

    <style>
        /* Container for the tab content */
        .tab-content {
            background: #ececec;
            padding: 10px;
        }

        /* Tab pane styling for better contrast */
        .tab-pane {
            padding: 20px;
            border: 1px solid #dee2e6;
            background: #ccc;
            border-top: 0;
            border-radius: 0 0 0.25rem 0.25rem;
        }
    </style>

    @switch($active_tab)
        @case('general')
            <div id="general" role="tabpanel" aria-labelledby="general-tab">
                <form action="{{ route('admin.hb837.update', ['id' => $hb837->id, 'tab_id' => 'general']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.hb837.partials.general', ['hb837' => $hb837])
                    <button type="submit" class="btn btn-success mt-4">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        @break

        @case('address')
            <div id="address" role="tabpanel" aria-labelledby="address-tab">
                <form action="{{ route('admin.hb837.update', ['id' => $hb837->id, 'tab_id' => 'address']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.hb837.partials.address', ['hb837' => $hb837])
                    <button type="submit" class="btn btn-success mt-4">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        @break

        @case('contacts')
            <div id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
                <form action="{{ route('admin.hb837.update', ['id' => $hb837->id, 'tab_id' => 'contacts']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.hb837.partials.contact', ['hb837' => $hb837])
                    <button type="submit" class="btn btn-success mt-4">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        @break

        @case('notes')
            <div id="notes" role="tabpanel" aria-labelledby="notes-tab">
                <form action="{{ route('admin.hb837.update', ['id' => $hb837->id, 'tab_id' => 'notes']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.hb837.partials.notes', ['hb837' => $hb837])
                    <button type="submit" class="btn btn-success mt-4">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        @break

        @case('financials')
            <div id="financials" role="tabpanel" aria-labelledby="financials-tab">
                <form action="{{ route('admin.hb837.update', ['id' => $hb837->id, 'tab_id' => 'financials']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.hb837.partials.financial', ['hb837' => $hb837])
                    <button type="submit" class="btn btn-success mt-4">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        @break

        @case('files')
            <div id="files" role="tabpanel" aria-labelledby="files-tab">
                <form action="{{ route('admin.hb837.update', ['id' => $hb837->id, 'tab_id' => 'files']) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.hb837.partials.files', ['tab' => 'files', 'hb837' => $hb837])
                    <button type="submit" class="btn btn-success mt-4">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        @break

        @case('google_maps')
            <div id="google_maps" role="tabpanel" aria-labelledby="maps-tab">
                @include('admin.hb837.partials.maps', ['hb837' => $hb837])
            </div>
        @break

        @default
            <div id="general" role="tabpanel" aria-labelledby="general-tab">
                <form action="{{ route('admin.hb837.update', ['id' => $hb837->id, 'tab_id' => 'general']) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.hb837.partials.general', ['hb837' => $hb837])
                    <button type="submit" class="btn btn-success mt-4">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        @break
    @endswitch
</div>
