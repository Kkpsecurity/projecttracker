{{-- Files Tab Content --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">File Management</h4>
            </div>
            <div class="card-body">
                <!-- File Upload Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5>Upload New File</h5>
                        <div class="form-group">
                            <label for="file_upload">Select File</label>
                            <input type="file" class="form-control-file" id="file_upload" name="file_upload" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                            <small class="form-text text-muted">
                                Accepted formats: PDF, Word documents, Excel files, Images (max 10MB)
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="file_description">File Description</label>
                            <input type="text" class="form-control" id="file_description" name="file_description" 
                                   placeholder="Brief description of the file">
                        </div>
                        <button type="button" class="btn btn-primary" onclick="uploadFile()">
                            <i class="fas fa-upload"></i> Upload File
                        </button>
                    </div>
                </div>

                <!-- Existing Files Section -->
                <div class="row">
                    <div class="col-12">
                        <h5>Existing Files</h5>
                        @if(isset($hb837->files) && $hb837->files->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Description</th>
                                            <th>Size</th>
                                            <th>Uploaded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hb837->files as $file)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file"></i> {{ $file->filename }}
                                            </td>
                                            <td>{{ $file->description ?? 'No description' }}</td>
                                            <td>{{ $file->file_size ? number_format($file->file_size / 1024, 1) . ' KB' : 'Unknown' }}</td>
                                            <td>{{ $file->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.hb837.files.download', $file->id) }}" 
                                                   class="btn btn-sm btn-info" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="deleteFile({{ $file->id }})" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No files have been uploaded for this property yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function uploadFile() {
    const fileInput = document.getElementById('file_upload');
    const descriptionInput = document.getElementById('file_description');
    
    if (!fileInput.files.length) {
        alert('Please select a file to upload.');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('description', descriptionInput.value);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Show loading state
    const uploadButton = event.target;
    const originalText = uploadButton.innerHTML;
    uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    uploadButton.disabled = true;
    
    fetch('{{ route("admin.hb837.files.upload", $hb837->id ?? 0) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('File uploaded successfully!');
            location.reload();
        } else {
            alert('Error uploading file: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading the file.');
    })
    .finally(() => {
        uploadButton.innerHTML = originalText;
        uploadButton.disabled = false;
    });
}

function deleteFile(fileId) {
    if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
        fetch(`{{ route("admin.hb837.files.delete", ":id") }}`.replace(':id', fileId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('File deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting file: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the file.');
        });
    }
}
</script>
