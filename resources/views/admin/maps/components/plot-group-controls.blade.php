{{-- Plot Group Controls Component --}}
<div class="mt-3">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="plot-group-filter">Load Plot Group:</label>
                <div class="input-group">
                    <select id="plot-group-filter" class="form-control">
                        <option value="">Select Plot Group...</option>
                        @foreach($plotGroups as $group)
                            <option value="{{ $group->id }}" data-color="{{ $group->color }}">
                                {{ $group->name }} ({{ $group->plots_count }} plots)
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-warning" type="button" id="load-group-plots-btn">
                            <i class="fas fa-layer-group"></i> Load Group
                        </button>
                    </div>
                </div>
                <small class="text-muted">View all plots from selected group</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="add-to-group-filter">Add Plot to Group:</label>
                <div class="plot-selection-info mb-2">
                    <small class="text-muted">Click on a plot marker to select it</small>
                </div>
                <div class="input-group">
                    <select id="add-to-group-filter" class="form-control">
                        <option value="">Select Group to Add To...</option>
                        @foreach($plotGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-success" type="button" id="add-to-group-btn" disabled>
                            <i class="fas fa-plus"></i> Add to Group
                        </button>
                    </div>
                </div>
                <small class="text-muted">Select plot on map and choose group to add</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Quick Actions:</label>
                <div class="btn-group d-block">
                    <button type="button" class="btn btn-primary btn-sm" id="create-new-group-btn" data-toggle="modal" data-target="#createGroupModal">
                        <i class="fas fa-plus-circle"></i> Create New Group
                    </button>
                    <a href="{{ route('admin.plot-groups.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-cog"></i> Manage Groups
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="clear-group-selection">
                        <i class="fas fa-eraser"></i> Clear Group
                    </button>
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

<script>
$(document).ready(function() {
    // Random color generator
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
                    location.reload(); // Refresh to show new group in dropdowns
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

    // Handle group selection change for add-to-group functionality
    $('#add-to-group-filter').change(function() {
        if (window.GoogleMapsCore) {
            window.GoogleMapsCore.updateSelectionUI();
        }
    });

    // Handle add plot to group functionality
    $('#add-to-group-btn').click(function() {
        if (!window.GoogleMapsCore || !window.GoogleMapsCore.selectedPlot) {
            alert('Please select a plot on the map first.');
            return;
        }

        const groupId = $('#add-to-group-filter').val();
        if (!groupId) {
            alert('Please select a group to add the plot to.');
            return;
        }

        const plotId = window.GoogleMapsCore.selectedPlot.id;
        const plotName = window.GoogleMapsCore.selectedPlot.plot_name || 'Plot ' + plotId;
        const groupName = $('#add-to-group-filter option:selected').text();

        if (confirm(`Add "${plotName}" to "${groupName}"?`)) {
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

            $.ajax({
                url: '{{ route("admin.plot-groups.add-plot", ":groupId") }}'.replace(':groupId', groupId),
                method: 'POST',
                data: {
                    plot_id: plotId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Plot added to group successfully!');
                        // Clear selection and refresh UI
                        window.GoogleMapsCore.clearSelection();
                        $('#add-to-group-filter').val('');
                        // Optionally reload page to refresh group counts
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to add plot to group'));
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to add plot to group. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert('Error: ' + errorMessage);
                },
                complete: function() {
                    $('#add-to-group-btn').prop('disabled', false).html('<i class="fas fa-plus"></i> Add to Group');
                }
            });
        }
    });

    // Handle clear group selection
    $('#clear-group-selection').click(function() {
        if (window.GoogleMapsCore) {
            window.GoogleMapsCore.clearSelection();
        }
        $('#add-to-group-filter').val('');
        $('#plot-group-filter').val('');
    });

    // Handle load group plots functionality
    $('#load-group-plots-btn').click(function() {
        const groupId = $('#plot-group-filter').val();
        if (!groupId) {
            alert('Please select a group to load.');
            return;
        }

        const groupName = $('#plot-group-filter option:selected').text();
        const groupColor = $('#plot-group-filter option:selected').data('color') || '#3498db';

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

        $.ajax({
            url: '{{ route("admin.plot-groups.api.group-plots", ":groupId") }}'.replace(':groupId', groupId),
            method: 'GET',
            success: function(response) {
                if (response.success && window.GoogleMapsCore) {
                    // Clear existing markers
                    window.GoogleMapsCore.clearMarkers();
                    
                    // Add group plots to map
                    if (response.plots && response.plots.length > 0) {
                        response.plots.forEach(plot => {
                            if (plot.coordinates_latitude && plot.coordinates_longitude) {
                                const marker = window.GoogleMapsCore.addMarker(plot);
                                // Change marker color to group color
                                marker.setIcon({
                                    url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                                    scaledSize: new google.maps.Size(32, 32)
                                });
                            }
                        });
                        
                        // Fit map to show all group plots
                        window.GoogleMapsCore.fitMapToBounds();
                        alert(`Loaded ${response.plots.length} plots from "${groupName}"`);
                    } else {
                        alert(`No plots found in "${groupName}"`);
                    }
                } else {
                    alert('Error loading group plots: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to load group plots. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert('Error: ' + errorMessage);
            },
            complete: function() {
                $('#load-group-plots-btn').prop('disabled', false).html('<i class="fas fa-layer-group"></i> Load Group');
            }
        });
    });
});
</script>
