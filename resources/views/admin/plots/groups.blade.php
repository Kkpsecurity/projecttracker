@extends('adminlte::page')

@section('title', 'Group Plots - Project Tracker')

@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-layer-group"></i> Group Plots</h1>
        <div>
            <a href="{{ route('admin.plot-clients.index') }}" class="btn btn-info">
                <i class="fas fa-building"></i> Macro Client Plots
            </a>
            <a href="{{ route('admin.maps.index') }}" class="btn btn-secondary">
                <i class="fas fa-map"></i> Google Maps
            </a>
        </div>
    </div>
@stop

@section('content')
    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total_groups'] }}</h3>
                    <p>Unassigned Groups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_unassigned_plots'] }}</h3>
                    <p>Unassigned Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['orphaned_plots'] }}</h3>
                    <p>Orphaned Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['available_for_assignment'] }}</h3>
                    <p>Ready for Grouping</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Grouping Actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group"></i> Group Management Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <button id="create-new-group-btn" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Group
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button id="merge-groups-btn" class="btn btn-info btn-block" disabled>
                                <i class="fas fa-object-group"></i> Merge Selected Groups
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button id="organize-plots-btn" class="btn btn-warning btn-block" disabled>
                                <i class="fas fa-sort"></i> Organize Plots
                            </button>
                        </div>
                        <!-- Convert to Client functionality ready but hidden for now -->
                        <div class="col-md-3 d-none">
                            <button id="convert-to-client-btn" class="btn btn-success btn-block" disabled>
                                <i class="fas fa-arrow-right"></i> Convert to Client
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Focus on organizing plots into logical groups. Create groups by location, property type, or other criteria to better manage your plot inventory.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Unassigned Plot Groups --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group"></i> Unassigned Plot Groups
                    </h3>
                </div>
                <div class="card-body">
                    @forelse($unassignedGroups as $group)
                        <div class="group-card border rounded p-3 mb-3" data-group-id="{{ $group->id }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>
                                        <span class="badge badge-primary" style="background-color: {{ $group->color }};">
                                            {{ $group->plots->count() }}
                                        </span>
                                        {{ $group->name }}
                                    </h5>
                                    @if($group->description)
                                        <p class="text-muted mb-2">{{ $group->description }}</p>
                                    @endif
                                    <small class="text-muted">
                                        Created: {{ $group->created_at->format('M j, Y') }} | 
                                        {{ $group->plots->count() }} plots in this group
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary edit-group-btn" data-group-id="{{ $group->id }}">
                                            <i class="fas fa-edit"></i> Edit Group
                                        </button>
                                        <button class="btn btn-sm btn-outline-info view-on-map-btn" data-group-id="{{ $group->id }}">
                                            <i class="fas fa-map"></i> View Map
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary select-group-btn" data-group-id="{{ $group->id }}">
                                            <i class="fas fa-check"></i> Select All
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="plots-container">
                                        @foreach($group->plots as $plot)
                                            <div class="plot-item d-inline-block m-1">
                                                <label class="badge badge-secondary plot-checkbox-label">
                                                    <input type="checkbox" class="plot-checkbox" value="{{ $plot->id }}" data-group-id="{{ $group->id }}">
                                                    <span class="ml-1">
                                                        {{ $plot->plot_name ?: 'Plot #' . $plot->id }}
                                                        @if($plot->address)
                                                            <br><small>{{ $plot->address->city }}, {{ $plot->address->state }}</small>
                                                        @endif
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                            <h4>No Plot Groups Found</h4>
                            <p class="text-muted">Create groups to organize your plots by location, type, or other criteria.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Orphaned Plots --}}
    @if($orphanedPlots->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle text-warning"></i> Orphaned Plots
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>These plots are not organized into any group.</strong>
                            Consider creating a group for them or adding them to existing groups to better organize your plot inventory.
                        </div>
                        
                        <div class="row">
                            @foreach($orphanedPlots as $plot)
                                <div class="col-md-3 mb-2">
                                    <label class="badge badge-warning plot-checkbox-label w-100">
                                        <input type="checkbox" class="plot-checkbox orphaned-plot" value="{{ $plot->id }}">
                                        <span class="ml-1">
                                            {{ $plot->plot_name ?: 'Plot #' . $plot->id }}
                                            @if($plot->address)
                                                <br><small>{{ $plot->address->street_address }}</small>
                                                <br><small>{{ $plot->address->city }}, {{ $plot->address->state }}</small>
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('admin.plot-clients.index') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-building"></i><br>
                                <strong>View Macro Clients</strong><br>
                                <small>See all assigned client plots</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.maps.index') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-map"></i><br>
                                <strong>Interactive Map</strong><br>
                                <small>View groups on map</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.plot-groups.index') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-cogs"></i><br>
                                <strong>Group Settings</strong><br>
                                <small>Create and edit plot groups</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create New Group Modal --}}
    <div class="modal fade" id="createGroupModal" tabindex="-1" role="dialog" aria-labelledby="createGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGroupModalLabel">
                        <i class="fas fa-plus-circle"></i> Create New Plot Group
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="create-group-form">
                        @csrf
                        <div class="form-group">
                            <label for="group-name">Group Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="group-name" name="name" required 
                                   placeholder="Enter group name...">
                            <small class="form-text text-muted">Choose a descriptive name for this plot group</small>
                        </div>
                        <div class="form-group">
                            <label for="group-description">Description</label>
                            <textarea class="form-control" id="group-description" name="description" rows="3" 
                                      placeholder="Optional description..."></textarea>
                            <small class="form-text text-muted">Add any additional details about this group</small>
                        </div>
                        <div class="form-group">
                            <label for="group-color">Group Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control" id="group-color" name="color" value="#3498db">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="random-color-btn">
                                        <i class="fas fa-dice"></i> Random
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Color used to display this group on the map</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-group-btn">
                        <i class="fas fa-save"></i> Create Group
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .group-card {
            background: #f8f9fa;
            transition: all 0.2s;
        }
        .group-card:hover {
            background: #e9ecef;
            border-color: #007bff !important;
        }
        .plot-checkbox-label {
            cursor: pointer;
            transition: all 0.2s;
        }
        .plot-checkbox-label:hover {
            background-color: #007bff !important;
            color: white;
        }
        .plot-checkbox:checked + span {
            text-decoration: line-through;
        }
        .selected-count {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let selectedPlots = [];

            // Handle individual plot selection
            $('.plot-checkbox').change(function() {
                const plotId = $(this).val();
                if ($(this).is(':checked')) {
                    selectedPlots.push(plotId);
                } else {
                    selectedPlots = selectedPlots.filter(id => id !== plotId);
                }
                updateSelectedCount();
                updateActionButtons();
            });

            // Handle group selection
            $('.select-group-btn').click(function() {
                const groupId = $(this).data('group-id');
                const groupCheckboxes = $(`.plot-checkbox[data-group-id="${groupId}"]`);
                const allSelected = groupCheckboxes.filter(':checked').length === groupCheckboxes.length;
                
                groupCheckboxes.prop('checked', !allSelected);
                groupCheckboxes.trigger('change');
                
                $(this).text(allSelected ? 'Select All' : 'Deselect All');
                $(this).find('i').attr('class', allSelected ? 'fas fa-check' : 'fas fa-times');
            });

            // Handle grouping actions
            $('#create-new-group-btn').click(function() {
                $('#createGroupModal').modal('show');
            });

            $('#merge-groups-btn').click(function() {
                if (selectedPlots.length === 0) {
                    alert('Please select plots or groups to merge.');
                    return;
                }
                // Future: Merge selected groups functionality
                alert('Merge Groups functionality coming soon!');
            });

            $('#organize-plots-btn').click(function() {
                if (selectedPlots.length === 0) {
                    alert('Please select plots to organize.');
                    return;
                }
                // Future: Organize plots functionality
                alert('Organize Plots functionality coming soon!');
            });

            // Handle group editing
            $('.edit-group-btn').click(function() {
                const groupId = $(this).data('group-id');
                // Future: Open edit group modal
                alert(`Edit Group ${groupId} functionality coming soon!`);
            });

            // View on map
            $('.view-on-map-btn').click(function() {
                const groupId = $(this).data('group-id');
                window.open(`{{ route('admin.maps.index') }}?plot_group=${groupId}`, '_blank');
            });

            function updateSelectedCount() {
                $('.selected-count').remove();
                if (selectedPlots.length > 0) {
                    $('body').append(`
                        <div class="selected-count">
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle"></i>
                                ${selectedPlots.length} plots selected
                            </div>
                        </div>
                    `);
                }
            }

            function updateActionButtons() {
                const hasSelection = selectedPlots.length > 0;
                
                $('#merge-groups-btn').prop('disabled', !hasSelection);
                $('#organize-plots-btn').prop('disabled', !hasSelection);
                // Convert to client functionality ready but hidden
                $('#convert-to-client-btn').prop('disabled', !hasSelection);
            }

            function assignPlotsToClient(plotIds, macroClient, projectName) {
                $.ajax({
                    url: '{{ route("admin.plot-groups-management.assign-to-client") }}',
                    method: 'POST',
                    data: {
                        plot_ids: plotIds,
                        macro_client: macroClient,
                        property_name: projectName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error assigning plots. Please try again.');
                    }
                });
            }

            // Initialize action buttons state
            updateActionButtons();

            // Create Group Modal functionality
            $('#random-color-btn').click(function() {
                const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e', '#e67e22'];
                const randomColor = colors[Math.floor(Math.random() * colors.length)];
                $('#group-color').val(randomColor);
            });

            // Create group form submission
            $('#save-group-btn').click(function() {
                const form = $('#create-group-form');
                const formData = {
                    name: $('#group-name').val().trim(),
                    description: $('#group-description').val().trim(),
                    color: $('#group-color').val(),
                    _token: form.find('[name="_token"]').val()
                };

                if (!formData.name) {
                    alert('Please enter a group name.');
                    $('#group-name').focus();
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

                $.ajax({
                    url: '{{ route("admin.plot-groups.store") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            alert('Group created successfully!');
                            $('#createGroupModal').modal('hide');
                            location.reload(); // Refresh to show new group
                        } else {
                            alert('Error: ' + (response.message || 'Failed to create group'));
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to create group. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        $('#save-group-btn').prop('disabled', false).html('<i class="fas fa-save"></i> Create Group');
                    }
                });
            });

            // Reset form when modal is hidden
            $('#createGroupModal').on('hidden.bs.modal', function() {
                $('#create-group-form')[0].reset();
                $('#group-color').val('#3498db');
            });
        });
    </script>
@stop
