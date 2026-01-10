{{-- File Management Tab Content --}}

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">File Management</h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                        <i class="fas fa-upload"></i> Upload File
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($hb837->files && $hb837->files->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Category</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hb837->files as $file)
                                    <tr>
                                        <td>{{ $file->original_filename }}</td>
                                        <td>{{ $file->file_category ?? 'General' }}</td>
                                        <td>{{ $file->file_size_human }}</td>
                                        <td>{{ $file->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ $file->download_url }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteFile({{ $file->id }})">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No files uploaded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
