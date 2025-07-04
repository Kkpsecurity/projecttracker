<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0">
            <i class="fas fa-clock-rotate-left me-2 text-secondary"></i>Backup History
        </h5>
        <div>
            <button class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>

    <div class="card-body">
        @include('partials.messages')

        @if (!$backups || $backups->isEmpty())
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-inbox fa-4x text-light" style="font-size: 5rem;"></i>
                </div>
                <h4 class="text-muted mb-2">No backups created yet</h4>
                <p class="text-muted mb-4">Your first backup will appear here</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#backupModal">
                    <i class="fas fa-plus me-1"></i>Create First Backup
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Backup Name</th>
                            <th>Created At</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $backup)
                            @include('admin.services.backup.partials.item_row', ['backup' => $backup])
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $backups->firstItem() }} to {{ $backups->lastItem() }} of {{ $backups->total() }} entries
                </div>
                <div>
                    {{ $backups->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Move all modals OUTSIDE of the table --}}
@if ($backups && $backups->isNotEmpty())
    @foreach ($backups as $backup)
        @include('partials.modals.backup_info_modal', ['backup' => $backup])
        @include('partials.modals.restore_modal', ['backup' => $backup])
    @endforeach
@endif
