<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Bulk Actions</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="bulk-actions-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bulk-action">Action</label>
                        <select id="bulk-action" name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="status_update">Update Status</option>
                            <option value="consultant_assign">Assign Consultant</option>
                            <option value="delete">Delete Records</option>
                        </select>
                    </div>

                    <div class="form-group" id="status-group" style="display: none;">
                        <label for="bulk-status">New Status</label>
                        <select id="bulk-status" name="bulk_status" class="form-control">
                            <option value="not-started">Not Started</option>
                            <option value="in-progress">In Progress</option>
                            <option value="in-review">In Review</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div class="form-group" id="consultant-group" style="display: none;">
                        <label for="bulk-consultant">Consultant</label>
                        <select id="bulk-consultant" name="bulk_consultant_id" class="form-control">
                            <option value="">Unassigned</option>
                            <!-- Populated via AJAX -->
                        </select>
                    </div>

                    <div id="selected-count" class="alert alert-info">
                        No records selected
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Execute Action</button>
                </div>
            </form>
        </div>
    </div>
</div>