<div class="modal fade" id="saveDataModal" tabindex="-1" role="dialog" aria-labelledby="savingDataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-shadow-sm" id="savingDataModalLabel">Saving Data</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3">
                <p id="exportMessage">Saving <strong>hb837</strong> and <strong>files</strong> table.</p>
                <button type="button" class="btn btn-primary" id="exportData">Confirm</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            <div class="progress" style="height: 5px; visibility: hidden;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" id="exportProgressBar"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modalEl = document.getElementById("saveDataModal");
        const saveDataModal = bootstrap.Modal.getOrCreateInstance(modalEl);

        // Example: manually close on button click
        document.getElementById("exportData").addEventListener("click", function () {
            saveDataModal.hide(); // ✅ closes the modal
        });
    });
    </script>

