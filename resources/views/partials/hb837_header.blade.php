@if ($status == 'active')
    <thead class="table-light">
        <tr>
            <th class="sticky-header">
                <div class="d-flex align-items-center justify-content-between">
                    Property
                    <button class="btn btn-sm btn-link sort p-0" data-sort="property_name">
                        &#x25B2;&#x25BC;
                    </button>
                </div>
            </th>
            <th class="sticky-header">
                <div class="d-flex align-items-center justify-content-between">
                    County
                    <button class="btn btn-sm btn-link sort p-0" data-sort="county">
                        &#x25B2;&#x25BC;
                    </button>
                </div>
            </th>
            <th class="sticky-header">
                <div class="d-flex align-items-center justify-content-between">
                    Company
                    <button class="btn btn-sm btn-link sort p-0" data-sort="management_company">
                        &#x25B2;&#x25BC;
                    </button>
                </div>
            </th>
            <th class="sticky-header">
                <div class="d-flex align-items-center justify-content-between">
                    Consultant
                    <button class="btn btn-sm btn-link sort p-0" data-sort="assigned_consultant">
                        &#x25B2;&#x25BC;
                    </button>
                </div>
            </th>
            <th class="sticky-header">
                <div class="d-flex align-items-center justify-content-between">
                    Inspection Date
                    <button class="btn btn-sm btn-link sort p-0" data-sort="inspection_date">
                        &#x25B2;&#x25BC;
                    </button>
                </div>
            </th>
            <th class="sticky-header">
                <div class="d-flex align-items-center justify-content-between">
                    Report Status
                    <button class="btn btn-sm btn-link sort p-0" data-sort="report_status">
                        &#x25B2;&#x25BC;
                    </button>
                </div>
            </th>
            <th class="sticky-header text-center">Action</th>
        </tr>
    </thead>
@endif
