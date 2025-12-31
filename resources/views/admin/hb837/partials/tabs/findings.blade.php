@php
    $findings = $hb837->findings()->latest()->get();
    $plots = $hb837->plots()->latest()->get();
    $findingCategories = config('hb837.finding_categories', []);
    $findingSeverities = config('hb837.finding_severities', []);
    $findingStatuses = config('hb837.finding_statuses', []);
@endphp

<div class="tab-header">
    <h4 class="tab-title"><i class="fas fa-clipboard-list"></i> Findings</h4>
    <p class="mb-0 text-muted">Create structured findings that will feed the final report.</p>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title" id="findingFormTitle">Add Finding</h3>
    </div>
    <div class="card-body">
        <input type="hidden" id="finding_id" value="">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="finding_category">Category</label>
                    <select class="form-control" id="finding_category" required>
                        <option value="">-- Select --</option>
                        @foreach($findingCategories as $option)
                            <option value="{{ $option }}">{{ ucwords(str_replace('-', ' ', $option)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="finding_severity">Severity</label>
                    <select class="form-control" id="finding_severity" required>
                        <option value="">-- Select --</option>
                        @foreach($findingSeverities as $option)
                            <option value="{{ $option }}">{{ ucwords(str_replace('-', ' ', $option)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="finding_status">Status</label>
                    <select class="form-control" id="finding_status" required>
                        <option value="">-- Select --</option>
                        @foreach($findingStatuses as $option)
                            <option value="{{ $option }}">{{ ucwords(str_replace('-', ' ', $option)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="finding_location_context">Location Context</label>
                    <input type="text" class="form-control" id="finding_location_context" placeholder="e.g. rear parking lot near building 3" required>
                </div>
            </div>
        </div>

        @if($plots->count() > 0)
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="finding_plot_id">Link to Plot (optional)</label>
                        <select class="form-control" id="finding_plot_id">
                            <option value="">-- None --</option>
                            @foreach($plots as $plot)
                                <option value="{{ $plot->id }}">Plot #{{ $plot->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @else
            <input type="hidden" id="finding_plot_id" value="">
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="finding_description">Description</label>
                    <textarea class="form-control" id="finding_description" rows="4" placeholder="What did you observe?" required></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="finding_recommendation">Recommendation</label>
                    <textarea class="form-control" id="finding_recommendation" rows="4" placeholder="What should be done?" required></textarea>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" id="findingCancelBtn" style="display:none;">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary" id="findingSaveBtn">
                <i class="fas fa-save"></i> Save Finding
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Existing Findings</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Severity</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($findings as $finding)
                        <tr data-finding='@json($finding)'>
                            <td>{{ $finding->category }}</td>
                            <td>{{ $finding->severity }}</td>
                            <td>{{ $finding->location_context }}</td>
                            <td>{{ $finding->status }}</td>
                            <td class="text-right">
                                <button type="button" class="btn btn-sm btn-info finding-edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger finding-delete-btn" data-finding-id="{{ $finding->id }}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No findings yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
(function() {
    const hb837Id = @json($hb837->id);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function resetFindingForm() {
        $('#findingFormTitle').text('Add Finding');
        $('#finding_id').val('');
        $('#finding_category').val('');
        $('#finding_severity').val('');
        $('#finding_status').val('new');
        $('#finding_location_context').val('');
        $('#finding_plot_id').val('');
        $('#finding_description').val('');
        $('#finding_recommendation').val('');
        $('#findingCancelBtn').hide();
        $('#findingSaveBtn').html('<i class="fas fa-save"></i> Save Finding');
    }

    function getPayload() {
        return {
            plot_id: $('#finding_plot_id').val() || null,
            category: $('#finding_category').val() || null,
            severity: $('#finding_severity').val() || null,
            status: $('#finding_status').val() || 'new',
            location_context: $('#finding_location_context').val() || null,
            description: $('#finding_description').val() || null,
            recommendation: $('#finding_recommendation').val() || null,
        };
    }

    $('#findingCancelBtn').on('click', function() {
        resetFindingForm();
    });

    $('.finding-edit-btn').on('click', function() {
        const row = $(this).closest('tr');
        const finding = row.data('finding');

        $('#findingFormTitle').text('Edit Finding');
        $('#finding_id').val(finding.id);
        $('#finding_category').val(finding.category || '');
        $('#finding_severity').val(finding.severity || '');
        $('#finding_status').val(finding.status || 'new');
        $('#finding_location_context').val(finding.location_context || '');
        $('#finding_plot_id').val(finding.plot_id || '');
        $('#finding_description').val(finding.description || '');
        $('#finding_recommendation').val(finding.recommendation || '');

        $('#findingCancelBtn').show();
        $('#findingSaveBtn').html('<i class="fas fa-save"></i> Update Finding');
    });

    $('.finding-delete-btn').on('click', function() {
        const findingId = $(this).data('finding-id');

        if (!confirm('Delete this finding?')) {
            return;
        }

        $.ajax({
            url: `/admin/hb837/${hb837Id}/findings/${findingId}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            success: function() {
                window.location.reload();
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Delete failed.';
                alert(message);
            }
        });
    });

    $('#findingSaveBtn').on('click', function() {
        const findingId = $('#finding_id').val();
        const isUpdate = !!findingId;

        const url = isUpdate
            ? `/admin/hb837/${hb837Id}/findings/${findingId}`
            : `/admin/hb837/${hb837Id}/findings`;

        $.ajax({
            url,
            method: isUpdate ? 'PUT' : 'POST',
            data: getPayload(),
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            success: function() {
                window.location.reload();
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Save failed.';
                alert(message);
            }
        });
    });

    // Initialize
    resetFindingForm();
})();
</script>
@endpush
