@extends('adminlte::page')

@section('title', 'HB837 Files')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>File Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.hb837.show', $hb837->id) }}">{{ $hb837->property_name }}</a></li>
                    <li class="breadcrumb-item active">Files</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Files for: {{ $hb837->property_name }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                            <i class="fas fa-upload"></i> Upload File
                        </button>
                        <a href="{{ route('admin.hb837.show', $hb837->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Record
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($hb837->files->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Filename</th>
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
                                            <i class="fas fa-file"></i>
                                            {{ $file->filename }}
                                        </td>
                                        <td>{{ $file->description ?: '-' }}</td>
                                        <td>{{ number_format($file->file_size / 1024, 1) }} KB</td>
                                        <td>{{ $file->created_at->format('M j, Y g:i A') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.hb837.files.download', $file->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="deleteFile({{ $file->id }})">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5><i class="icon fas fa-info-circle"></i> No Files</h5>
                            <p class="mb-0">No files have been uploaded for this HB837 record yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.hb837.files.upload', $hb837->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Select File <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" required>
                                <label class="custom-file-label" for="file">Choose file...</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">Maximum file size: 10 MB</small>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" 
                               placeholder="Optional file description">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
// Update file input label with selected filename
document.getElementById('file').addEventListener('change', function(e) {
    var fileName = e.target.files[0]?.name || 'Choose file...';
    var label = e.target.nextElementSibling;
    label.textContent = fileName;
});

// Delete file function
function deleteFile(fileId) {
    if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
        fetch(`{{ route('admin.hb837.files.delete', '') }}/${fileId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting file: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error deleting file: ' + error.message);
        });
    }
}
</script>
@stop
