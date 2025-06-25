<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="importModalLabel"><i class="fas fa-file-import me-2"></i>Import HB837 Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="importForm" action="<?php echo e(route('admin.hb837.backup.import')); ?>" method="POST" enctype="multipart/form-data" onsubmit="return confirmImport()">
                <?php echo csrf_field(); ?>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Choose Excel/CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,.xlsx" required>
                        <div class="form-text">Only files exported from this system are supported.</div>
                    </div>

                    <div class="mb-3">
                        <label for="import_title" class="form-label">Import Title (auto-generated if left blank)</label>
                        <input type="text" name="import_title" id="import_title" class="form-control" placeholder="e.g. <?php echo e(now()->format('Y-m-d')); ?> - [First Address in File]">
                        <div class="form-text">If left blank, a title will be generated using the current date and the first address in your file.</div>
                    </div>

                    <?php if(auth()->id() == 1): ?>
                        <div class="alert alert-info small mb-3">
                            <strong>Debug:</strong> Authenticated as admin (User ID 1). Debugging and admin-only features are visible.
                        </div>
                    <?php endif; ?>

                    <?php if(auth()->id() == 1 || auth()->id() == 2): ?>
                        <div class="form-check mb-3 border border-warning rounded p-3 bg-light">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-shield-alt text-warning me-2"></i>
                                <strong class="text-warning">Admin Only - Debugging Feature</strong>
                            </div>
                            <input class="form-check-input" type="checkbox" id="truncateData" name="truncate">
                            <label class="form-check-label" for="truncateData">
                                <strong>Fresh Import (This will truncate all HB837 data)</strong>
                            </label>
                            <div class="form-text text-muted mt-1">
                                <i class="fas fa-exclamation-triangle text-danger me-1"></i>
                                <strong>Warning:</strong> This will permanently delete all existing HB837 records before importing new data. Use only for debugging or fresh system setup.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    // Add confirmation dialog for truncate checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const truncateCheckbox = document.getElementById('truncateData');
        if (truncateCheckbox) {
            truncateCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    const confirmed = confirm(
                        '⚠️ WARNING: You are about to enable TRUNCATE mode!\n\n' +
                        'This will PERMANENTLY DELETE all existing HB837 records before importing new data.\n\n' +
                        'This action cannot be undone.\n\n' +
                        'Are you sure you want to proceed?'
                    );

                    if (!confirmed) {
                        this.checked = false;
                    }
                }
            });
        }
    });

    // Final confirmation before import submission
    function confirmImport() {
        const truncateCheckbox = document.getElementById('truncateData');

        if (truncateCheckbox && truncateCheckbox.checked) {
            return confirm(
                '🚨 FINAL WARNING: TRUNCATE MODE ENABLED!\n\n' +
                'You are about to:\n' +
                '• PERMANENTLY DELETE all existing HB837 records\n' +
                '• Import new data from the selected file\n\n' +
                'THIS CANNOT BE UNDONE!\n\n' +
                'Type "DELETE ALL DATA" in the next prompt to confirm...'
            ) && prompt('Type "DELETE ALL DATA" to confirm:') === 'DELETE ALL DATA';
        }

        return true;
    }
</script>
<?php /**PATH /var/www/projecttracker/resources/views/partials/modals/export_import_modal.blade.php ENDPATH**/ ?>