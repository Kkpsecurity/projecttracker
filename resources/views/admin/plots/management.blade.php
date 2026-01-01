@extends('adminlte::page')

@section('title', 'Plot Management Dashboard')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Plot Management Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Plot Management</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_plots'] }}</h3>
                    <p>Total Plots</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['assigned_plots'] }}</h3>
                    <p>Assigned to Clients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['grouped_plots'] }}</h3>
                    <p>In Groups</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['orphaned_plots'] }}</h3>
                    <p>Unassigned</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" id="search-input" class="form-control" placeholder="Search plots, addresses, clients...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select id="filter-status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="grouped">Grouped</option>
                                    <option value="unassigned">Unassigned</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" id="toggle-bulk-select">
                                    <i class="fas fa-check-square"></i> Bulk Select
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clear-filters">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group float-right" role="group">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-plot-modal">
                                    <i class="fas fa-plus"></i> Create Plot
                                </button>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#create-group-modal">
                                    <i class="fas fa-layer-group"></i> Create Group
                                </button>
                                <a href="{{ route('admin.maps.index') }}" class="btn btn-primary">
                                    <i class="fas fa-map"></i> Map View
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions Bar (hidden by default) -->
                    <div id="bulk-actions-bar" class="row mt-3" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span id="selected-count">0</span> plots selected
                                    </div>
                                    <div class="col-md-6">
                                        <div class="btn-group float-right" role="group">
                                            <button type="button" class="btn btn-sm btn-success" id="bulk-assign-client">
                                                <i class="fas fa-user-plus"></i> Assign to Client
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" id="bulk-move-group">
                                                <i class="fas fa-arrow-right"></i> Move to Group
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" id="bulk-remove-assignment">
                                                <i class="fas fa-user-minus"></i> Remove Assignment
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Macro Clients Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Macro Clients
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="macro-clients-container">
                        @forelse($macroClients as $client)
                            <div class="client-section mb-4" data-client="{{ $client['name'] }}">
                                <div class="client-header">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="mb-0">
                                                <i class="fas fa-chevron-right client-toggle" style="cursor: pointer;"></i>
                                                <strong>{{ $client['name'] }}</strong>
                                                <span class="badge badge-primary ml-2">{{ $client['plots_count'] }} plots</span>
                                                <span class="badge badge-secondary ml-1">{{ $client['projects_count'] }} projects</span>
                                            </h5>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary add-plot-to-client" data-client="{{ $client['name'] }}">
                                                    <i class="fas fa-plus"></i> Add Plot
                                                </button>
                                                <button type="button" class="btn btn-outline-success view-on-map" data-client="{{ $client['name'] }}">
                                                    <i class="fas fa-map-marker-alt"></i> Map
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="client-plots" style="display: none;">
                                    <div class="mt-3">
                                        @foreach($client['plots'] as $plot)
                                            <div class="plot-item border-left-primary" data-plot-id="{{ $plot->id }}">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1">
                                                        <input type="checkbox" class="plot-checkbox" value="{{ $plot->id }}" style="display: none;">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <h6 class="mb-1">{{ $plot->plot_name ?? 'Unnamed Plot' }}</h6>
                                                        @if($plot->address)
                                                            <small class="text-muted">{{ $plot->address->address_line_1 ?? 'No address' }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        @if($plot->hb837)
                                                            <span class="badge badge-success">{{ $plot->hb837->property_name }}</span>
                                                        @endif
                                                        @foreach($plot->plotGroups as $group)
                                                            <span class="badge badge-info">{{ $group->name }}</span>
                                                        @endforeach
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                                                            <span class="badge badge-success"><i class="fas fa-map-marker-alt"></i> Mapped</span>
                                                        @else
                                                            <span class="badge badge-warning"><i class="fas fa-exclamation"></i> No Coords</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2 text-right">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="{{ route('admin.plots.show', $plot->id) }}" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('admin.plots.edit', $plot->id) }}" class="btn btn-outline-warning btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger btn-sm remove-plot" data-plot-id="{{ $plot->id }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">
                                <p>No macro clients found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unassigned Plot Groups Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group"></i> Unassigned Plot Groups
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="unassigned-groups-container">
                        @forelse($unassignedGroups as $group)
                            <div class="group-section mb-4" data-group-id="{{ $group->id }}">
                                <div class="group-header">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="mb-0">
                                                <i class="fas fa-chevron-right group-toggle" style="cursor: pointer;"></i>
                                                <strong>{{ $group->name }}</strong>
                                                <span class="badge badge-warning ml-2">{{ $group->plots->count() }} plots</span>
                                            </h5>
                                            @if($group->description)
                                                <small class="text-muted">{{ $group->description }}</small>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-success bulk-assign-group" data-group-id="{{ $group->id }}">
                                                    <i class="fas fa-user-plus"></i> Assign to Client
                                                </button>
                                                <button type="button" class="btn btn-outline-primary add-plot-to-group" data-group-id="{{ $group->id }}">
                                                    <i class="fas fa-plus"></i> Add Plot
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="group-plots" style="display: none;">
                                    <div class="mt-3">
                                        @foreach($group->plots as $plot)
                                            <div class="plot-item border-left-warning" data-plot-id="{{ $plot->id }}">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1">
                                                        <input type="checkbox" class="plot-checkbox" value="{{ $plot->id }}" style="display: none;">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <h6 class="mb-1">{{ $plot->plot_name ?? 'Unnamed Plot' }}</h6>
                                                        @if($plot->address)
                                                            <small class="text-muted">{{ $plot->address->address_line_1 ?? 'No address' }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <span class="badge badge-secondary">Unassigned</span>
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                                                            <span class="badge badge-success"><i class="fas fa-map-marker-alt"></i> Mapped</span>
                                                        @else
                                                            <span class="badge badge-warning"><i class="fas fa-exclamation"></i> No Coords</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2 text-right">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-success btn-sm assign-single-plot" data-plot-id="{{ $plot->id }}">
                                                                <i class="fas fa-user-plus"></i> Assign
                                                            </button>
                                                            <a href="{{ route('admin.plots.edit', $plot->id) }}" class="btn btn-outline-warning btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">
                                <p>No unassigned plot groups found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orphaned Plots Section -->
    @if($orphanedPlots->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Orphaned Plots
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <p><strong>Note:</strong> These plots are not assigned to any client or plot group.</p>
                    </div>
                    <div id="orphaned-plots-container">
                        @foreach($orphanedPlots as $plot)
                            <div class="plot-item border-left-danger" data-plot-id="{{ $plot->id }}">
                                <div class="row align-items-center">
                                    <div class="col-md-1">
                                        <input type="checkbox" class="plot-checkbox" value="{{ $plot->id }}" style="display: none;">
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="mb-1">{{ $plot->plot_name ?? 'Unnamed Plot' }}</h6>
                                        @if($plot->address)
                                            <small class="text-muted">{{ $plot->address->address_line_1 ?? 'No address' }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <span class="badge badge-danger">Orphaned</span>
                                    </div>
                                    <div class="col-md-2">
                                        @if($plot->coordinates_latitude && $plot->coordinates_longitude)
                                            <span class="badge badge-success"><i class="fas fa-map-marker-alt"></i> Mapped</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-exclamation"></i> No Coords</span>
                                        @endif
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success btn-sm assign-single-plot" data-plot-id="{{ $plot->id }}">
                                                <i class="fas fa-user-plus"></i> Assign
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm add-to-group-single" data-plot-id="{{ $plot->id }}">
                                                <i class="fas fa-layer-group"></i> Group
                                            </button>
                                            <a href="{{ route('admin.plots.edit', $plot->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@stop

@section('css')
<style>
.plot-item {
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 5px;
    background: #f8f9fa;
    border-left: 3px solid #007bff;
}

.plot-item.border-left-primary {
    border-left-color: #007bff;
}

.plot-item.border-left-warning {
    border-left-color: #ffc107;
}

.plot-item.border-left-danger {
    border-left-color: #dc3545;
}

.client-section, .group-section {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    background: #ffffff;
}

.client-header, .group-header {
    cursor: pointer;
}

.client-toggle, .group-toggle {
    transition: transform 0.3s ease;
}

.client-toggle.expanded, .group-toggle.expanded {
    transform: rotate(90deg);
}

.plot-checkbox:checked + * {
    background-color: #e3f2fd;
}

.bulk-select-active .plot-checkbox {
    display: inline-block !important;
}

.search-highlight {
    background-color: yellow;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    let bulkSelectMode = false;
    
    // Toggle bulk select mode
    $('#toggle-bulk-select').click(function() {
        bulkSelectMode = !bulkSelectMode;
        
        if (bulkSelectMode) {
            $('body').addClass('bulk-select-active');
            $('.plot-checkbox').show();
            $('#bulk-actions-bar').show();
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        } else {
            $('body').removeClass('bulk-select-active');
            $('.plot-checkbox').hide().prop('checked', false);
            $('#bulk-actions-bar').hide();
            $(this).removeClass('btn-primary').addClass('btn-outline-primary');
            updateSelectedCount();
        }
    });
    
    // Update selected count
    function updateSelectedCount() {
        const count = $('.plot-checkbox:checked').length;
        $('#selected-count').text(count);
        
        // Enable/disable bulk action buttons
        if (count > 0) {
            $('#bulk-actions-bar .btn').prop('disabled', false);
        } else {
            $('#bulk-actions-bar .btn').prop('disabled', true);
        }
    }
    
    // Plot checkbox change
    $(document).on('change', '.plot-checkbox', function() {
        updateSelectedCount();
    });
    
    // Toggle client/group sections
    $(document).on('click', '.client-toggle, .group-toggle', function() {
        const $this = $(this);
        const $container = $this.closest('.client-section, .group-section');
        const $plots = $container.find('.client-plots, .group-plots');
        
        $plots.slideToggle();
        $this.toggleClass('expanded');
    });
    
    // Search functionality
    $('#search-input').on('input', function() {
        const query = $(this).val().toLowerCase();
        
        $('.plot-item').each(function() {
            const $item = $(this);
            const text = $item.text().toLowerCase();
            
            if (text.includes(query)) {
                $item.show();
            } else {
                $item.hide();
            }
        });
        
        // Hide empty sections
        $('.client-section, .group-section').each(function() {
            const $section = $(this);
            const visiblePlots = $section.find('.plot-item:visible').length;
            
            if (visiblePlots > 0) {
                $section.show();
            } else {
                $section.hide();
            }
        });
    });
    
    // Filter by status
    $('#filter-status').change(function() {
        const status = $(this).val();
        
        if (status === '') {
            $('.client-section, .group-section, #orphaned-plots-container').show();
            $('.plot-item').show();
        } else if (status === 'assigned') {
            $('.client-section').show();
            $('.group-section, #orphaned-plots-container').hide();
        } else if (status === 'grouped') {
            $('.group-section').show();
            $('.client-section, #orphaned-plots-container').hide();
        } else if (status === 'unassigned') {
            $('#orphaned-plots-container').parent().show();
            $('.client-section, .group-section').hide();
        }
    });
    
    // Clear filters
    $('#clear-filters').click(function() {
        $('#search-input').val('');
        $('#filter-status').val('');
        $('.client-section, .group-section, .plot-item').show();
        $('#orphaned-plots-container').parent().show();
    });
    
    // Bulk assign to client
    $('#bulk-assign-client').click(function() {
        const selectedPlots = $('.plot-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selectedPlots.length === 0) {
            alert('Please select plots to assign.');
            return;
        }
        
        // Show assignment modal
        showAssignmentModal(selectedPlots);
    });
    
    // Bulk move to group
    $('#bulk-move-group').click(function() {
        const selectedPlots = $('.plot-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selectedPlots.length === 0) {
            alert('Please select plots to move.');
            return;
        }
        
        // Show group assignment modal
        showGroupAssignmentModal(selectedPlots);
    });
    
    // Remove assignment
    $('#bulk-remove-assignment').click(function() {
        const selectedPlots = $('.plot-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selectedPlots.length === 0) {
            alert('Please select plots to remove assignment.');
            return;
        }
        
        if (confirm('Are you sure you want to remove assignment from ' + selectedPlots.length + ' plots?')) {
            removeAssignment(selectedPlots);
        }
    });
    
    // Functions for modal dialogs and AJAX calls
    function showAssignmentModal(plotIds) {
        // Implementation will go here
        console.log('Show assignment modal for plots:', plotIds);
    }
    
    function showGroupAssignmentModal(plotIds) {
        // Implementation will go here
        console.log('Show group assignment modal for plots:', plotIds);
    }
    
    function removeAssignment(plotIds) {
        // Implementation will go here
        console.log('Remove assignment for plots:', plotIds);
    }
});
</script>
@stop
