 <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="fileUploadForm" action="{{ route('admin.hb837.files.upload', $hb837->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Select File</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
                        </div>
                        <div class="form-group">
                            <label for="file_category">Category</label>
                            <select class="form-control" id="file_category" name="file_category">
                                <option value="general">General</option>
                                <option value="report">Report</option>
                                <option value="contract">Contract</option>
                                <option value="assessment">Assessment</option>
                                <option value="correspondence">Correspondence</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="fileUploadForm" class="btn btn-primary">Upload File</button>
                </div>
            </div>
        </div>
    </div>