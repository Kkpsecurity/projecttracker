{{-- Plot Actions Partial --}}
<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('admin.plots.show', $plot->id) }}"
       class="btn btn-info btn-sm"
       title="View Plot">
        <i class="fas fa-eye"></i>
    </a>

    <a href="{{ route('admin.plots.edit', $plot->id) }}"
       class="btn btn-warning btn-sm"
       title="Edit Plot">
        <i class="fas fa-edit"></i>
    </a>

    @if($plot->coordinates_latitude && $plot->coordinates_longitude)
        <a href="{{ route('admin.maps.plot.show', $plot->id) }}"
           class="btn btn-success btn-sm"
           title="View on Map">
            <i class="fas fa-map-marker-alt"></i>
        </a>
    @endif

    <button type="button"
            class="btn btn-danger btn-sm"
            onclick="deletePlot({{ $plot->id }})"
            title="Delete Plot">
        <i class="fas fa-trash"></i>
    </button>
</div>

<script>
function deletePlot(plotId) {
    if (confirm('Are you sure you want to delete this plot?')) {
        fetch(`/admin/plots/${plotId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload DataTable
                if (window.plotsTable) {
                    window.plotsTable.ajax.reload();
                } else {
                    location.reload();
                }

                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message);
                } else {
                    alert(data.message);
                }
            } else {
                alert('Error: ' + (data.message || 'Failed to delete plot'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the plot');
        });
    }
}
</script>
