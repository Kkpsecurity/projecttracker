<?php $__env->startSection('styles'); ?>
    <style>
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            border-radius: 0.75rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.03);
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            padding: 1rem 1.5rem;
            font-weight: 500;
            color: #6c757d;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom-color: #0d6efd;
            background: transparent;
        }

        .stat-card-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .backup-item {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s;
        }

        .backup-item:hover {
            background-color: #f8f9fa;
            border-left-color: #198754;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 text-light">
            <div>
                <h1 class="h3 mb-0"><i class="fas fa-database me-2 text-success"></i>Data Management</h1>
                <p class="text-white mb-0">Manage backups and data exports</p>
            </div>
            <div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fa fa-plus me-1"></i>Import Record
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4 bg-light" id="backupTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="hb837-tab" data-bs-toggle="tab" href="#hb837" role="tab"
                    aria-controls="hb837" aria-selected="true">
                    <i class="fa fa-database me-1"></i> HB837 Backup
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="imports-tab" data-bs-toggle="tab" href="#imports" role="tab"
                    aria-controls="imports" aria-selected="false">
                    <i class="fa fa-uploads me-1"></i>Imports
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="system-tab" data-bs-toggle="tab" href="#system" role="tab"
                    aria-controls="system" aria-selected="false">
                    <i class="fa fa-server me-1"></i> Full System Backup
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content bg-white p-5" id="backupTabsContent">
            <div class="tab-pane fade show active" id="hb837" role="tabpanel" aria-labelledby="hb837-tab">
                <?php echo $__env->make('admin.services.backup.stats_cards', [
                    'backups' => $backups,
                    'stats' => $stats,
                    'systemStats' => $systemStats,
                    'recentActivity' => $recentActivity,
                ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo $__env->make('admin.services.backup.action_cards', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo $__env->make('admin.services.backup.backup_history', ['backups' => $backups], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                <?php echo $__env->make('admin.services.backup.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="tab-pane fade" id="imports" role="tabpanel" aria-labelledby="imports-tab">
                <?php echo $__env->make('admin.services.backup.imports', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="backupSuccessToast" class="toast align-items-center text-bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i> Backup completed successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>


    <?php echo $__env->make('partials.modals.export_import_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.modals.backup_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements initialization
            const progressEl = document.getElementById('backupProgress');
            const progressBar = document.querySelector('.progress-bar');
            const timeRemaining = document.getElementById('timeRemaining');
            const backupForm = document.getElementById('backupForm');
            const submitBtn = backupForm?.querySelector('button[type="submit"]');
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');

            // Create progress steps container
            let stepsEl = null;
            if (progressEl) {
                stepsEl = document.createElement('div');
                stepsEl.className = 'mt-3 small text-muted';
                progressEl.appendChild(stepsEl);
            }

            // Initialize tooltips
            [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));

            async function submitBackup(event) {
                event.preventDefault();

                // Form validation
                if (!backupForm) return;

                // Check if at least one table is selected
                const tableCheckboxes = backupForm.querySelectorAll('input[name="tables[]"]:checked');
                if (tableCheckboxes.length === 0) {
                    alert('Please select at least one table to backup.');
                    return;
                }

                const formData = new FormData(backupForm);

                // Debug: Log form data
                console.log('Form data being sent:');
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                // UI Reset
                resetProgressUI();
                disableSubmitButton();

                try {
                    const response = await fetch(backupForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        console.error('Server response:', data);
                        throw new Error(`Server responded with ${response.status}: ${data.message || 'Unknown error'}`);
                    }

                    handleBackupSuccess(data);
                } catch (error) {
                    handleBackupError(error);
                }
            }

            function resetProgressUI() {
                if (stepsEl) stepsEl.innerHTML = '';
                if (progressBar) {
                    progressBar.style.width = '0%';
                    progressBar.textContent = '0%';
                }
                if (timeRemaining) timeRemaining.textContent = 'Processing...';
                if (progressEl) progressEl.classList.remove('d-none');
            }

            function disableSubmitButton() {
                if (!submitBtn) return;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i>Processing...';
            }

            function resetSubmitButton() {
                if (!submitBtn) return;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-database me-2"></i>Create Backup';
            }

            function handleBackupSuccess(data) {
                // Update progress UI
                if (progressBar) {
                    progressBar.style.width = '100%';
                    progressBar.textContent = '100%';
                }
                if (timeRemaining) timeRemaining.textContent = 'Done';
                if (stepsEl && data.message) {
                    stepsEl.innerHTML += `<div><i class="fas fa-check-circle text-success me-1"></i>${data.message}</div>`;
                }

                // Finalize process
                setTimeout(() => {
                    const backupModal = document.getElementById('backupModal');
                    if (backupModal) {
                        bootstrap.Modal.getInstance(backupModal)?.hide();
                    }

                    const toastEl = document.getElementById('backupSuccessToast');
                    if (toastEl) {
                        new bootstrap.Toast(toastEl).show();
                    }

                    setTimeout(() => location.reload(), 1500);
                }, 1500);
            }

            function handleBackupError(error) {
                console.error('Backup error:', error);

                // Try to parse detailed validation errors
                if (error.message.includes('422')) {
                    alert(`Validation failed. Please check the form and try again.\n\nError: ${error.message}`);
                } else {
                    alert(`Backup failed: ${error.message}`);
                }

                resetSubmitButton();
            }

            // Event listeners
            if (backupForm) {
                backupForm.addEventListener('submit', submitBackup);
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/projecttracker/resources/views/admin/services/backup/index.blade.php ENDPATH**/ ?>