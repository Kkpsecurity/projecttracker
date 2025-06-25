<tr class="backup-item">
    <td>
        <div class="d-flex align-items-center">
            <i class="fas fa-file-excel text-success fa-2x me-3"></i>
            <div>
                <strong>{{ $backup->name ?: 'Backup #' . $backup->id }}</strong>
                <div class="text-muted small">ID: {{ $backup->id }}</div>
            </div>
        </div>
    </td>

    <td>
        <div>{{ $backup->created_at->format('M j, Y') }}</div>
        <div class="text-muted small">{{ $backup->created_at->format('g:i A') }}</div>
    </td>

    <td>{{ number_format($backup->size / 1024, 2) }} KB</td>

    <!--Displays what tables was backedup #?-->
    <td>
        <a href="{{ route('admin.hb837.backup.download', $backup->filename) }}" class="btn btn-sm btn-light text-success"
            title="Download">
            <i class="fa fa-download"></i>
        </a>
        @php
            $tables = is_array($backup->tables)
                ? $backup->tables
                : (is_string($backup->tables)
                    ? array_map('trim', explode(',', $backup->tables))
                    : []);
        @endphp

        @if (count($tables))
            {{ implode(' | ', $tables) }}
        @else
            <span class="text-muted">No tables</span>
        @endif
    </td>

    <td class="text-end">
        <div class="btn-group">

            <button class="btn btn-sm btn-light text-info" data-bs-toggle="modal"
                data-bs-target="#infoModal{{ $backup->id }}" title="Details">
                <i class="fa fa-info-circle"></i>
            </button>
            <button class="btn btn-sm btn-light text-warn" data-bs-toggle="modal"
                data-bs-target="#restoreModal{{ $backup->id }}" title="Restore">
                <i class="fa fa-refresh"></i>
            </button>
            <form action="{{ route('admin.hb837.backup.delete_file', $backup->uuid) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-light text-danger"
                    onclick="return confirm('Are you sure you want to delete this backup?')" title="Delete">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
