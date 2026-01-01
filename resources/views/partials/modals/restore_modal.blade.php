<!-- Restore Modal -->
<div class="modal fade" id="restoreModal{{ $backup->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title text-shadow-sm"><i class="fa fa-history me-2"></i>Restore Backup – {{ $backup->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('admin.hb837.backup.restore', $backup->uuid) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light border-danger border-start border-3">
                        <i class="fa fa-exclamation-triangle me-2 text-warning"></i>
                        <strong>Warning:</strong> This will overwrite all current HB837 project data using the file:
                        <br><code>{{ $backup->filename }}</code>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmRestore{{ $backup->id }}" name="confirm_restore" required>
                        <label class="form-check-label" for="confirmRestore{{ $backup->id }}">
                            I understand this will replace all current HB837 data
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-upload me-2"></i>Restore Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
