@php
$active_tab = Request()->segment(4) ?? 'Active';

$tabs = [
    'general' => [
        'icon' => 'fa-info-circle',
        'name' => 'General',
        'default' => true,
    ],
    'address' => [
        'icon' => 'fa-map-marker',
        'name' => 'Address',
    ],
    'contact' => [
        'icon' => 'fa-envelope',
        'name' => 'Contact',
    ],
    'consultant' => [
        'icon' => 'fa-sticky-note',
        'name' => 'Notes',
    ],
    'financial' => [
        'icon' => 'fa-dollar',
        'name' => 'Financial',
    ],
    'files' => [
        'icon' => 'fa-file',
        'name' => 'Files',
    ],
    'google_maps' => [
        'icon' => 'fa-map',
        'name' => 'Google Maps',
    ],
];
@endphp


<ul class="nav nav-tabs" id="editTabs" role="tablist">
    @foreach ($tabs as $tab_id => $tab)
        <a href="{{ url('admin/hb837/' . $hb837->id . '/edit/' . $tab_id ) }}"
            class="lms-tabs btn btn-default {{ $tab_id == $active_tab ? 'active' : '' }}">
            <i class="fa {{ $tab['icon'] }}"></i> @lang(ucwords($tab['name']))
        </a>
    @endforeach
</ul>
