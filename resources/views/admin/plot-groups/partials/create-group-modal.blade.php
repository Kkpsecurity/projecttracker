 <div class="modal fade" id="create-group-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Plot Group</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="create-group-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="group-name">Group Name *</label>
                            <input type="text" class="form-control" id="group-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="group-description">Description</label>
                            <textarea class="form-control" id="group-description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="group-color">Marker Color</label>
                            <input type="color" class="form-control" id="group-color" name="color" value="#3498db">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Group</button>
                    </div>
                </form>
            </div>
        </div>
    </div>