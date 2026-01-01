<div                 <div class="modal-header">
                    <h5 class="modal-title text-shadow-sm" id="createPlotModalLabel">Create New Plot</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">ss="modal fade" id="createPlotModal" tabindex="-1" aria-labelledby="createPlotModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.mapplots.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPlotModalLabel">Create New Plot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="plot_name" class="form-label">Plot Name</label>
                        <input type="text" class="form-control" id="plot_name" name="plot_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Plot</button>
                </div>
            </form>
        </div>
    </div>
</div>
