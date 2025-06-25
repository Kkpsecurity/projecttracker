<!-- Include FontAwesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<section style="min-height: 450px; height: auto;">
    <div class="form-group mt-3">
        <label for="related_files bolder">Upload Related Files</label>
        <input type="file" name="related_files[]" id="related_files" class="form-control" multiple>
        @error('related_files')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @error('related_files.*')
            <div class="invalid-feedback d-block fs-12" style="font-size: 12px;">{{ $message }}</div>
        @enderror
    </div>

    @php
        function getIconType($file)
        {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $icon = 'fa-file';
            switch ($ext) {
                case 'pdf':
                    $icon = 'fa-file-pdf';
                    break;
                case 'doc':
                case 'docx':
                    $icon = 'fa-file-word';
                    break;
                case 'xls':
                case 'xlsx':
                    $icon = 'fa-file-excel';
                    break;
                case 'ppt':
                case 'pptx':
                    $icon = 'fa-file-powerpoint';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    $icon = 'fa-file-image';
                    break;
                case 'zip':
                case 'rar':
                    $icon = 'fa-file-archive';
                    break;
                case 'txt':
                    $icon = 'fa-file-alt';
                    break;
                case 'mp3':
                case 'wav':
                    $icon = 'fa-file-audio';
                    break;
                case 'mp4':
                case 'avi':
                case 'mov':
                    $icon = 'fa-file-video';
                    break;
                case 'php':
                case 'js':
                case 'css':
                case 'html':
                    $icon = 'fa-file-code';
                    break;
                default:
                    $icon = 'fa-file';
                    break;
            }
            return $icon;
        }
    @endphp

    @if ($hb837->files ?? false)
        <h5 class="mt-4">Existing Files</h5>
        <ul class="list-group">
            @foreach ($hb837->files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.hb837.download', basename($file->file_path)) }}" class="d-flex align-items-center">
                        <i class="fa {{ getIconType($file->filename) }} fa-fw fa-2x text-dark"></i>
                    </a>

                    <div class="flex-grow-1 ms-3">
                        <p class="mb-0">
                            <strong>File Name:</strong> {{ $file->filename }} <br />
                            <small class="text-muted">Uploaded At: {{ $file->created_at }}</small>
                        </p>
                    </div>

                    <div class="form-check ms-3">
                        <input class="form-check-input" type="checkbox" name="delete_files[]"
                            value="{{ $file->id }}" id="delete_file_{{ $file->id }}">
                        <label class="form-check-label" for="delete_file_{{ $file->id }}">
                            Delete
                        </label>
                    </div>
                </li>
            @endforeach
        </ul>
        </ul>
    @endif
</section>
