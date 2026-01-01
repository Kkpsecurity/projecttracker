{{-- Tab Navigation --}}
<ul class="nav nav-tabs" id="editTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab === 'general' ? 'active' : '' }}" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="{{ $tab === 'general' ? 'true' : 'false' }}" title="General">
            <i class="fas fa-home"></i> Gen
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab === 'address' ? 'active' : '' }}" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="{{ $tab === 'address' ? 'true' : 'false' }}" title="Address">
            <i class="fas fa-map-marker-alt"></i> Addr
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab === 'contact' ? 'active' : '' }}" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="{{ $tab === 'contact' ? 'true' : 'false' }}" title="Contact">
            <i class="fas fa-users"></i> Contact
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab === 'financial' ? 'active' : '' }}" id="financial-tab" data-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="{{ $tab === 'financial' ? 'true' : 'false' }}" title="Financial">
            <i class="fas fa-dollar-sign"></i> Fin
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab === 'notes' ? 'active' : '' }}" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="{{ $tab === 'notes' ? 'true' : 'false' }}" title="Notes">
            <i class="fas fa-sticky-note"></i> Notes
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab === 'files' ? 'active' : '' }}" id="files-tab" data-toggle="tab" href="#files" role="tab" aria-controls="files" aria-selected="{{ $tab === 'files' ? 'true' : 'false' }}" title="Files">
            <i class="fas fa-file-alt"></i> Files
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $tab === 'maps' ? 'active' : '' }}" id="maps-tab" data-toggle="tab" href="#maps" role="tab" aria-controls="maps" aria-selected="{{ $tab === 'maps' ? 'true' : 'false' }}" title="Maps">
            <i class="fas fa-map"></i> Maps
        </a>
    </li>
</ul>
