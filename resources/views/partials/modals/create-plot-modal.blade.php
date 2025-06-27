<div class="modal fade" id="createPlotModal" tabindex="-1" aria-labelledby="createPlotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.mapplots.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPlotModalLabel">Create New Plot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="plot_name" class="form-label">Plot Name *</label>
                                <input type="text" class="form-control" id="plot_name" name="plot_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="plot_type" class="form-label">Plot Type *</label>
                                <select class="form-select" id="plot_type" name="plot_type" required>
                                    <option value="custom" selected>Custom Plot</option>
                                    <option value="prospect">Prospect</option>
                                    <option value="client">Client</option>
                                </select>
                                <div class="form-text">
                                    <small><strong>Custom:</strong> Temporary grouping or testing<br>
                                    <strong>Prospect:</strong> Potential new client<br>
                                    <strong>Client:</strong> Existing property management client</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Optional description of this plot..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div id="client-contact-fields" style="display: none;">
                                <h6 class="mb-3 text-primary">Client Contact Information</h6>
                                
                                <div class="mb-3">
                                    <label for="client_contact_name" class="form-label">Contact Name</label>
                                    <input type="text" class="form-control" id="client_contact_name" name="client_contact_name">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="client_contact_email" class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" id="client_contact_email" name="client_contact_email">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="client_contact_phone" class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control" id="client_contact_phone" name="client_contact_phone">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active Plot
                                    </label>
                                    <div class="form-text">Inactive plots are hidden from the main interface</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Plot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const plotTypeSelect = document.getElementById('plot_type');
    const clientContactFields = document.getElementById('client-contact-fields');
    
    function toggleClientFields() {
        const plotType = plotTypeSelect.value;
        if (plotType === 'prospect' || plotType === 'client') {
            clientContactFields.style.display = 'block';
            if (plotType === 'client') {
                document.getElementById('client_contact_name').required = true;
            }
        } else {
            clientContactFields.style.display = 'none';
            document.getElementById('client_contact_name').required = false;
        }
    }
    
    plotTypeSelect.addEventListener('change', toggleClientFields);
    toggleClientFields(); // Initial call
});
</script>
