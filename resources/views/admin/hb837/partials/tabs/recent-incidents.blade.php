@php
    $incidents = $hb837->recentIncidents()->orderBy('sort_order')->orderBy('id')->get();
@endphp

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title" id="incidentFormTitle">Add Incident</h3>
    </div>
    <div class="card-body">
        <input type="hidden" id="incident_id" value="">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="incident_date">Date (label)</label>
                    <input type="text" class="form-control" id="incident_date" placeholder="e.g. Summer 2025, 2024-2025">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="incident_sort_order">Sort</label>
                    <input type="number" class="form-control" id="incident_sort_order" min="0" step="1" value="0">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="incident_summary">Summary of Incident</label>
                    <textarea class="form-control" id="incident_summary" rows="4" placeholder="Short narrative description of event and outcome." required></textarea>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="button" class="btn btn-secondary" id="incidentCancelBtn" style="display:none;">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-primary" id="incidentSaveBtn">
                <i class="fas fa-save"></i> Save Incident
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Incidents</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 10%;">Sort</th>
                        <th style="width: 20%;">Date</th>
                        <th>Summary</th>
                        <th style="width: 18%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="incidentRows">
                    @forelse($incidents as $i)
                        <tr data-id="{{ $i->id }}">
                            <td class="text-muted">{{ $i->sort_order }}</td>
                            <td>{{ $i->incident_date ?: 'N/A' }}</td>
                            <td>{{ $i->summary }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary incident-edit">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger incident-delete">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No incidents yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function initHb837RecentIncidents(attempt) {
        attempt = attempt || 0;

        if (!window.jQuery) {
            if (attempt < 100) {
                return window.setTimeout(function() { initHb837RecentIncidents(attempt + 1); }, 50);
            }
            return;
        }

        var $ = window.jQuery;

        $(function() {
            const hb837Id = {{ (int) $hb837->id }};
            const baseUrl = `{{ url('admin/hb837') }}/${hb837Id}/recent-incidents`;

            const $formTitle = $('#incidentFormTitle');
            const $id = $('#incident_id');
            const $date = $('#incident_date');
            const $summary = $('#incident_summary');
            const $sort = $('#incident_sort_order');

            const $cancelBtn = $('#incidentCancelBtn');
            const $saveBtn = $('#incidentSaveBtn');

        function resetForm() {
            $id.val('');
            $date.val('');
            $summary.val('');
            $sort.val('0');
            $formTitle.text('Add Incident');
            $cancelBtn.hide();
        }

        function toRowHtml(i) {
            const safeDate = i.incident_date ? $('<div>').text(i.incident_date).html() : 'N/A';
            const safeSummary = i.summary ? $('<div>').text(i.summary).html() : '';

            return `
                <tr data-id="${i.id}">
                    <td class="text-muted">${i.sort_order ?? 0}</td>
                    <td>${safeDate}</td>
                    <td>${safeSummary}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary incident-edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger incident-delete">Delete</button>
                    </td>
                </tr>
            `;
        }

        function upsertRow(i) {
            const $existing = $(`#incidentRows tr[data-id="${i.id}"]`);
            if ($existing.length) {
                $existing.replaceWith(toRowHtml(i));
                return;
            }

            const $empty = $('#incidentRows tr td[colspan="4"]');
            if ($empty.length) {
                $('#incidentRows').html(toRowHtml(i));
                return;
            }

            $('#incidentRows').append(toRowHtml(i));
        }

        $saveBtn.on('click', function() {
            const payload = {
                incident_date: $date.val() || null,
                summary: $summary.val() || '',
                sort_order: parseInt($sort.val() || '0', 10)
            };

            const idVal = $id.val();
            const isEdit = !!idVal;
            const url = isEdit ? `${baseUrl}/${idVal}` : baseUrl;
            const method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url,
                type: method,
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(resp) {
                    if (resp && resp.incident) {
                        upsertRow(resp.incident);
                        resetForm();
                    }
                },
                error: function(xhr) {
                    const msg = xhr?.responseJSON?.message || 'Failed to save incident.';
                    alert(msg);
                }
            });
        });

        $cancelBtn.on('click', function() {
            resetForm();
        });

        $(document).on('click', '.incident-edit', function() {
            const $tr = $(this).closest('tr');
            const idVal = $tr.data('id');
            const sortVal = $tr.find('td').eq(0).text().trim();
            const dateVal = $tr.find('td').eq(1).text().trim();
            const summaryVal = $tr.find('td').eq(2).text().trim();

            $id.val(idVal);
            $sort.val(sortVal || '0');
            $date.val(dateVal === 'N/A' ? '' : dateVal);
            $summary.val(summaryVal);
            $formTitle.text('Edit Incident');
            $cancelBtn.show();
        });

        $(document).on('click', '.incident-delete', function() {
            if (!confirm('Delete this incident?')) {
                return;
            }

            const $tr = $(this).closest('tr');
            const idVal = $tr.data('id');

            $.ajax({
                url: `${baseUrl}/${idVal}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function() {
                    $tr.remove();
                    if ($('#incidentRows tr').length === 0) {
                        $('#incidentRows').html('<tr><td colspan="4" class="text-center text-muted py-4">No incidents yet.</td></tr>');
                    }
                    resetForm();
                },
                error: function(xhr) {
                    const msg = xhr?.responseJSON?.message || 'Failed to delete incident.';
                    alert(msg);
                }
            });
        });
        });
    })(0);
</script>
