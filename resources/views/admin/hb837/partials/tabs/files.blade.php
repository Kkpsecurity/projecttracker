{{-- File Management Tab Content --}}

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Crime Report Files</h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                        <i class="fas fa-upload"></i> Upload (Crime PDF / Slot Image)
                    </button>
                </div>
            </div>
            <div class="card-body">
                @php
                    $crimeFiles = ($hb837->files ?? collect())->where('file_category', 'crime_report');
                @endphp

                @if($crimeFiles->count() > 0)
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
                                @foreach($crimeFiles as $file)
                                    <tr>
                                        <td>{{ $file->original_filename }}</td>
                                        <td>{{ $file->file_category ?? 'General' }}</td>
                                        <td>{{ $file->file_size_human }}</td>
                                        <td>{{ $file->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ $file->download_url }}" class="btn btn-sm btn-info" target="_blank" rel="noopener">
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
                        <p class="text-muted">No crime report files uploaded yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Slot Images</h5>
            </div>
            <div class="card-body">
                @php
                    $slotFiles = ($hb837->files ?? collect())
                        ->whereIn('file_category', ['appendix', 'photo', 'page_3'])
                        ->whereNotNull('file_position');
                @endphp

                @if($slotFiles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped" id="hb837-slot-images-table">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Page</th>
                                    <th>Position</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($slotFiles as $file)
                                    @php
                                        $categoryLabels = [
                                            'map_screenshot' => 'Site Map / Diagram (Screenshot)',
                                            'page_3' => 'Page 3 (8 Slots)',
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $file->original_filename }}</td>
                                        <td>{{ $categoryLabels[$file->file_category] ?? ucwords(str_replace(['_', '-'], ' ', $file->file_category ?? '')) }}</td>
                                        <td>{{ $file->file_position ? ucwords(str_replace(['_', '-'], ' ', $file->file_position)) : '—' }}</td>
                                        <td>{{ $file->file_size_human }}</td>
                                        <td>{{ $file->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ $file->download_url }}" class="btn btn-sm btn-info" target="_blank" rel="noopener">
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
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No slot images uploaded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
