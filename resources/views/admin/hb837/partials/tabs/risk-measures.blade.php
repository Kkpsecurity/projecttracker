@php
    $riskMeasures = $hb837->riskMeasures()->orderBy('section')->orderBy('sort_order')->orderBy('id')->get();
    $riskMeasureSections = config('hb837.risk_measure_sections', ['4.1','4.2','4.3','4.4','4.5','4.6']);
    $riskMeasureCbRanks = config('hb837.risk_measure_cb_ranks', ['CB1','CB2','CB3','CB4']);
@endphp

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title" id="riskMeasureFormTitle">Add Measure</h3>
    </div>
    <div class="card-body">
        <input type="hidden" id="risk_measure_id" value="">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="risk_measure_section">Section</label>
                    <select class="form-control" id="risk_measure_section" required>
                        @foreach($riskMeasureSections as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="risk_measure_no">No. (optional)</label>
                    <input type="number" class="form-control" id="risk_measure_no" min="1" step="1" placeholder="e.g. 1">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="risk_measure_cb_rank">CB Rank</label>
                    <select class="form-control" id="risk_measure_cb_rank">
                        <option value="">--</option>
                        @foreach($riskMeasureCbRanks as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="risk_measure_sort_order">Sort</label>
                    <input type="number" class="form-control" id="risk_measure_sort_order" min="0" step="1" value="0">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="risk_measure_text">Risk Reduction Measure</label>
                    <textarea class="form-control" id="risk_measure_text" rows="4" placeholder="Type the measure exactly as it should appear in the report" required></textarea>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" id="riskMeasureCancelBtn" style="display:none;">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary" id="riskMeasureSaveBtn">
                <i class="fas fa-save"></i> Save Measure
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Existing Measures</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Section</th>
                        <th>No.</th>
                        <th>CB</th>
                        <th>Measure</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riskMeasures as $m)
                        <tr data-risk-measure='@json($m)'>
                            <td>{{ $m->section }}</td>
                            <td>{{ $m->measure_no ?: '' }}</td>
                            <td>{{ $m->cb_rank ?: '' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($m->measure, 140) }}</td>
                            <td class="text-right">
                                <button type="button" class="btn btn-sm btn-info risk-measure-edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger risk-measure-delete-btn" data-risk-measure-id="{{ $m->id }}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No measures yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
(function initHb837RiskMeasures(attempt) {
    attempt = attempt || 0;

    if (!window.jQuery) {
        if (attempt < 100) {
            return window.setTimeout(function() { initHb837RiskMeasures(attempt + 1); }, 50);
        }
        return;
    }

    var $ = window.jQuery;

    $(function() {
        const hb837Id = @json($hb837->id);
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function resetRiskMeasureForm() {
        $('#riskMeasureFormTitle').text('Add Measure');
        $('#risk_measure_id').val('');
        $('#risk_measure_section').val('4.1');
        $('#risk_measure_no').val('');
        $('#risk_measure_cb_rank').val('');
        $('#risk_measure_sort_order').val('0');
        $('#risk_measure_text').val('');
        $('#riskMeasureCancelBtn').hide();
        $('#riskMeasureSaveBtn').html('<i class="fas fa-save"></i> Save Measure');
    }

    function getPayload() {
        const measureNo = $('#risk_measure_no').val();
        return {
            section: $('#risk_measure_section').val() || null,
            measure_no: measureNo ? parseInt(measureNo, 10) : null,
            cb_rank: $('#risk_measure_cb_rank').val() || null,
            sort_order: parseInt($('#risk_measure_sort_order').val() || '0', 10),
            measure: $('#risk_measure_text').val() || null,
        };
    }

    $('#riskMeasureCancelBtn').on('click', function() {
        resetRiskMeasureForm();
    });

    $('.risk-measure-edit-btn').on('click', function() {
        const row = $(this).closest('tr');
        const measure = row.data('risk-measure');

        $('#riskMeasureFormTitle').text('Edit Measure');
        $('#risk_measure_id').val(measure.id);
        $('#risk_measure_section').val(measure.section || '4.1');
        $('#risk_measure_no').val(measure.measure_no || '');
        $('#risk_measure_cb_rank').val(measure.cb_rank || '');
        $('#risk_measure_sort_order').val(measure.sort_order ?? 0);
        $('#risk_measure_text').val(measure.measure || '');

        $('#riskMeasureCancelBtn').show();
        $('#riskMeasureSaveBtn').html('<i class="fas fa-save"></i> Update Measure');
    });

    $('.risk-measure-delete-btn').on('click', function() {
        const id = $(this).data('risk-measure-id');

        if (!confirm('Delete this measure?')) {
            return;
        }

        $.ajax({
            url: `/admin/hb837/${hb837Id}/risk-measures/${id}`,
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

    $('#riskMeasureSaveBtn').on('click', function() {
        const id = $('#risk_measure_id').val();
        const isUpdate = !!id;

        const url = isUpdate
            ? `/admin/hb837/${hb837Id}/risk-measures/${id}`
            : `/admin/hb837/${hb837Id}/risk-measures`;

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

        resetRiskMeasureForm();
    });
})(0);
</script>
@endpush
