@php
    $statuteTemplate = [
        [
            'key' => 'cctv_system',
            'label' => 'CCTV System',
            'statute' => 'Fla. Stat. § 768.0706(2)(a)(1)',
            'text' => 'A security camera system at points of entry and exit which records, and maintains as retrievable for at least 30 days, video footage to assist in offender identification and apprehension.',
            'sort_order' => 10,
        ],
        [
            'key' => 'parking_lot_illumination',
            'label' => 'Parking Lot Illumination',
            'statute' => 'Fla. Stat. § 768.0706(2)(a)(2)',
            'text' => 'A lighted parking lot illuminated at an intensity of at least an average of 1.8 foot-candles per square foot at 18 inches above the surface from dusk until dawn or controlled by photocell or any similar electronic device that provides light from dusk until dawn.',
            'sort_order' => 20,
        ],
        [
            'key' => 'other_lighting',
            'label' => 'Other Lighting',
            'statute' => 'Fla. Stat. § 768.0706(2)(a)(3)',
            'text' => 'Lighting in walkways, laundry rooms, common areas, and porches. Such lighting must be illuminated from dusk until dawn or controlled by photocell or any similar electronic device that provides light from dusk until dawn.',
            'sort_order' => 30,
        ],
        [
            'key' => 'deadbolt_locks',
            'label' => 'Deadbolt Locks',
            'statute' => 'Fla. Stat. § 768.0706(2)(a)(4)',
            'text' => 'At least a 1-inch deadbolt in each dwelling unit door.',
            'sort_order' => 40,
        ],
        [
            'key' => 'locking_devices',
            'label' => 'Locking Devices',
            'statute' => 'Fla. Stat. § 768.0706(2)(a)(5)',
            'text' => 'A locking device on each window, each exterior sliding door, and any other doors not used for community purposes.',
            'sort_order' => 50,
        ],
        [
            'key' => 'pool_access',
            'label' => 'Pool Access',
            'statute' => 'Fla. Stat. § 768.0706(2)(a)(6)',
            'text' => 'Locked gates with key or fob access along pool fence areas.',
            'sort_order' => 60,
        ],
        [
            'key' => 'peepholes',
            'label' => 'Peepholes/Door Viewers',
            'statute' => 'Fla. Stat. § 768.0706(2)(a)(7)',
            'text' => 'A peephole or door viewer on each dwelling unit door that does not include a window or that does not have a window next to the door.',
            'sort_order' => 70,
        ],
    ];

    $existing = $hb837->statuteConditions()->get()->keyBy('condition_key');
@endphp

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 32%;">Condition</th>
                        <th style="width: 18%;">Status</th>
                        <th>Observations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statuteTemplate as $t)
                        @php
                            $row = $existing->get($t['key']);
                            $status = $row?->status;
                            $obs = $row?->observations;
                        @endphp
                        <tr data-key="{{ $t['key'] }}" data-sort="{{ $t['sort_order'] }}">
                            <td>
                                <div style="font-weight: 600;">{{ $t['label'] }}</div>
                                <div class="small text-muted">{{ $t['statute'] }}</div>
                                <div class="small text-muted">{{ $t['text'] }}</div>
                            </td>
                            <td>
                                <select class="form-control form-control-sm statute-status">
                                    <option value="" {{ $status ? '' : 'selected' }}>—</option>
                                    <option value="compliant" {{ $status === 'compliant' ? 'selected' : '' }}>Compliant</option>
                                    <option value="non_compliant" {{ $status === 'non_compliant' ? 'selected' : '' }}>Non-compliant</option>
                                    <option value="unknown" {{ $status === 'unknown' ? 'selected' : '' }}>Unknown</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control form-control-sm statute-observations" rows="4" placeholder="Enter consultant observations...">{{ $obs }}</textarea>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-right mt-3">
            <button type="button" class="btn btn-primary" id="saveStatuteConditionsBtn">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>
</div>

<script>
    (function initHb837StatuteConditions(attempt) {
        attempt = attempt || 0;

        if (!window.jQuery) {
            if (attempt < 100) {
                return window.setTimeout(function() { initHb837StatuteConditions(attempt + 1); }, 50);
            }
            return;
        }

        var $ = window.jQuery;

        $(function() {
            const hb837Id = {{ (int) $hb837->id }};
            const url = `{{ url('admin/hb837') }}/${hb837Id}/statute-conditions`;

            $('#saveStatuteConditionsBtn').on('click', function() {
                const conditions = [];
                $('tr[data-key]').each(function() {
                    const $tr = $(this);
                    conditions.push({
                        condition_key: $tr.data('key'),
                        sort_order: parseInt($tr.data('sort') || '0', 10),
                        status: $tr.find('.statute-status').val() || null,
                        observations: $tr.find('.statute-observations').val() || null,
                    });
                });

                $.ajax({
                    url,
                    type: 'POST',
                    data: { conditions },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        alert('Saved.');
                    },
                    error: function(xhr) {
                        const msg = xhr?.responseJSON?.message || 'Failed to save statute conditions.';
                        alert(msg);
                    }
                });
            });
        });
    })(0);
</script>
