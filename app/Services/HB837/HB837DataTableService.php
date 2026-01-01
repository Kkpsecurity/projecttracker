<?php

namespace App\Services\HB837;

use App\Models\HB837;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HB837DataTableService
{
    /**
     * Get DataTables data with optimized performance
     */
    public function getDatatablesData(string $tab, \Illuminate\Http\Request $request = null): \Illuminate\Http\JsonResponse
    {
        try {
            $query = HB837::query()->with(['consultant', 'user']);

            // Apply tab filters
            $this->applyTabFilters($query, $tab);

            // Apply additional request filters (Task 18 Enhancement)
            $this->applyRequestFilters($query);

            return DataTables::of($query)
                ->addColumn('checkbox', fn($hb837) => $this->renderCheckbox($hb837))
                ->addColumn('overdue_status_badge', fn($hb837) => $this->renderOverdueStatusBadge($hb837))
                ->addColumn('action', fn($hb837) => $this->renderActionButtons($hb837))
                ->editColumn('property_name', fn($hb837) => $this->renderPropertyName($hb837))
                ->editColumn('report_status', fn($hb837) => $this->renderReportStatus($hb837))
                ->editColumn('assigned_consultant_id', fn($hb837) => $this->renderConsultant($hb837))
                ->editColumn('scheduled_date_of_inspection', fn($hb837) => $this->renderScheduledDate($hb837))
                ->editColumn('county', fn($hb837) => $this->renderCounty($hb837))
                ->addColumn('type_unit_type', fn($hb837) => $this->renderTypeUnit($hb837))
                ->editColumn('macro_client', fn($hb837) => $this->renderMacroClient($hb837))
                ->editColumn('securitygauge_crime_risk', fn($hb837) => $this->renderCrimeRisk($hb837))
                ->editColumn('contracting_status', fn($hb837) => $this->renderContractingStatus($hb837))
                ->editColumn('agreement_submitted', fn($hb837) => $this->renderAgreementSubmitted($hb837))
                ->editColumn('quoted_price', fn($hb837) => $this->renderQuotedPrice($hb837))
                ->addColumn('days_until_renewal', fn($hb837) => $this->renderDaysUntilRenewal($hb837))
                ->addColumn('is_thirty_day_overdue', fn($hb837) => $this->getThirtyDayOverdueFlag($hb837))
                ->addColumn('is_overdue', fn($hb837) => $hb837->is_overdue)
                ->addColumn('days_since_created', fn($hb837) => now()->diffInDays($hb837->created_at))
                ->editColumn('billing_req_submitted', fn($hb837) => $this->renderBillingRequest($hb837))
                ->rawColumns($this->getRawColumns())
                ->orderColumn('assigned_consultant_id', function ($query, $order) {
                    return $this->orderByConsultant($query, $order);
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables getDatatablesData error for tab ' . $tab . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'draw' => request('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Apply tab filters to query
     */
    protected function applyTabFilters($query, string $tab): void
    {
        switch ($tab) {
            case 'all':
                // No filters - show all records
                break;
            case 'active':
                $query->whereIn('report_status', ['not-started', 'underway', 'in-review'])
                    ->where('contracting_status', 'executed');
                break;
            case 'quoted':
                $query->whereIn('contracting_status', ['quoted', 'started']);
                break;
            case 'completed':
                $query->where('report_status', 'completed');
                break;
            case 'closed':
                $query->where('contracting_status', 'closed');
                break;
        }
    }

    /**
     * Apply additional request filters for DataTables (Task 18 Enhancement)
     */
    protected function applyRequestFilters($query): void
    {
        $request = request();

        // Status filter
        if ($request->filled('status')) {
            $query->where('report_status', $request->status);
        }

        // Consultant filter
        if ($request->filled('consultant')) {
            $query->where('assigned_consultant_id', $request->consultant);
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 30-day overdue filter (Task 18 Enhancement)
        if ($request->filled('show_thirty_day_overdue') && $request->show_thirty_day_overdue) {
            $thirtyDaysAgo = now()->subDays(30);
            $query->where('created_at', '<', $thirtyDaysAgo)
                ->whereNotIn('report_status', ['completed']);
        }
    }

    /**
     * Render checkbox column
     */
    private function renderCheckbox(HB837 $hb837): string
    {
        return '<input type="checkbox" class="bulk-checkbox" value="' . $hb837->id . '">';
    }

    /**
     * Render overdue status badge (Task 18 Enhancement)
     */
    private function renderOverdueStatusBadge(HB837 $hb837): string
    {
        $daysSinceCreated = now()->diffInDays($hb837->created_at);
        $isIncomplete = !in_array($hb837->report_status, ['completed']);

        if ($isIncomplete && $daysSinceCreated > 30) {
            return '<span class="badge thirty-day-overdue-badge status-badge-critical">30+ Days</span>';
        } elseif ($hb837->is_overdue) {
            return '<span class="badge status-badge-overdue">Overdue</span>';
        } elseif ($daysSinceCreated > 14 && $isIncomplete) {
            return '<span class="badge badge-warning">14+ Days</span>';
        }

        return '<span class="badge badge-success">Current</span>';
    }

    /**
     * Render action buttons
     */
    private function renderActionButtons(HB837 $hb837): string
    {
        $editUrl = route('admin.hb837.edit', ['hb837' => $hb837->id]);
        $pdfUrl = route('admin.hb837.pdf-report', ['hb837' => $hb837->id]);

        return '
            <div class="btn-group btn-group-sm" role="group">
                <button onclick="viewPropertyLocation(' . $hb837->id . ', \'' . addslashes($hb837->property_name) . '\', \'' . addslashes($hb837->address) . '\', \'' . addslashes($hb837->city) . '\', \'' . addslashes($hb837->state) . '\')"
                        class="btn btn-info" title="View Location" data-toggle="tooltip">
                    <i class="fas fa-map-marker-alt"></i>
                </button>
                <a href="' . $editUrl . '"
                   class="btn btn-primary" title="Edit Record" data-toggle="tooltip">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="' . $pdfUrl . '"
                   class="btn btn-warning" title="PDF Report" data-toggle="tooltip">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <button onclick="deleteRecord(' . $hb837->id . ')"
                        class="btn btn-danger" title="Delete Record" data-toggle="tooltip">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        ';
    }

    /**
     * Render property name with address
     */
    private function renderPropertyName(HB837 $hb837): string
    {
        return '<strong>' . e($hb837->property_name) . '</strong><br>
                <small class="text-muted">' . e($hb837->address) . ', ' . e($hb837->city) . ', ' . e($hb837->state) . '</small>';
    }

    /**
     * Render report status badge
     */
    private function renderReportStatus(HB837 $hb837): string
    {
        if (!$hb837->report_status) return '<span class="text-muted">No status</span>';

        $colors = [
            'not-started' => ['bg' => '#f8d7da', 'text' => '#721c24'],
            'underway' => ['bg' => '#fff3cd', 'text' => '#856404'],
            'in-review' => ['bg' => '#cce5ff', 'text' => '#004085'],
            'completed' => ['bg' => '#d4edda', 'text' => '#155724']
        ];

        $style = isset($colors[$hb837->report_status]) ?
            'background-color: ' . $colors[$hb837->report_status]['bg'] . '; color: ' . $colors[$hb837->report_status]['text'] . ';' : '';

        $displayStatus = ucfirst(str_replace('-', ' ', $hb837->report_status));
        return '<span class="badge px-3 py-2" style="' . $style . '">' . e($displayStatus) . '</span>';
    }

    /**
     * Render consultant name
     */
    private function renderConsultant(HB837 $hb837): string
    {
        return $hb837->consultant ?
            $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name :
            '<span class="text-muted">Unassigned</span>';
    }

    /**
     * Render scheduled date with late report styling
     */
    private function renderScheduledDate(HB837 $hb837): string
    {
        if ($hb837->scheduled_date_of_inspection) {
            $date = \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection);

            // Apply red bold styling for "Late Reports" criteria
            $isLateReport = $hb837->report_status === 'not-started'
                && $hb837->contracting_status === 'executed'
                && $date->lt(now()->subDays(30));

            $class = $isLateReport ? 'text-danger font-weight-bold' : '';
            return '<span class="' . $class . '">' . $date->format('M j, Y') . '</span>';
        }
        return '<span class="text-muted">Not scheduled</span>';
    }

    /**
     * Render county
     */
    private function renderCounty(HB837 $hb837): string
    {
        return $hb837->county ?: '<span class="text-muted">Not specified</span>';
    }

    /**
     * Render type and unit information
     */
    private function renderTypeUnit(HB837 $hb837): string
    {
        $typeText = $hb837->property_type ?: 'Unknown Type';
        $typeText = ucfirst($typeText);
        $unitsText = $hb837->units ? $hb837->units . ' units' : 'No units';
        return e($typeText) . '<br><small class="text-muted">' . e($unitsText) . '</small>';
    }

    /**
     * Render macro client
     */
    private function renderMacroClient(HB837 $hb837): string
    {
        return $hb837->macro_client ?: '<span class="text-muted">Not assigned</span>';
    }

    /**
     * Render crime risk with enhanced styling
     */
    private function renderCrimeRisk(HB837 $hb837): string
    {
        if (!$hb837->securitygauge_crime_risk) {
            return '<span class="text-muted" style="font-size: 14px; font-weight: 500;">Not Assessed</span>';
        }

        $cssClasses = [
            'Low' => 'risk-low',
            'Moderate' => 'risk-moderate',
            'Elevated' => 'risk-elevated',
            'High' => 'risk-high',
            'Severe' => 'risk-severe'
        ];

        $class = isset($cssClasses[$hb837->securitygauge_crime_risk]) ? $cssClasses[$hb837->securitygauge_crime_risk] : '';

        return '<span class="badge px-3 py-2 ' . $class . '" style="font-size: 13px; font-weight: 500;">' . e($hb837->securitygauge_crime_risk) . '</span>';
    }

    /**
     * Render contracting status
     */
    private function renderContractingStatus(HB837 $hb837): string
    {
        if (!$hb837->contracting_status) return '<span class="text-muted">No status</span>';

        $colors = [
            'quoted' => ['bg' => '#e3f2fd', 'text' => '#0d47a1'],
            'started' => ['bg' => '#fff8e1', 'text' => '#e65100'],
            'executed' => ['bg' => '#e8f5e8', 'text' => '#2e7d32'],
            'closed' => ['bg' => '#fce4ec', 'text' => '#c2185b']
        ];

        $style = isset($colors[$hb837->contracting_status]) ?
            'background-color: ' . $colors[$hb837->contracting_status]['bg'] . '; color: ' . $colors[$hb837->contracting_status]['text'] . ';' : '';

        $displayStatus = ucfirst(str_replace('-', ' ', $hb837->contracting_status));
        return '<span class="badge px-3 py-2" style="' . $style . '">' . e($displayStatus) . '</span>';
    }

    /**
     * Render agreement submitted status
     */
    private function renderAgreementSubmitted(HB837 $hb837): string
    {
        if ($hb837->agreement_submitted) {
            $date = \Carbon\Carbon::parse($hb837->agreement_submitted);
            return '<span class="badge badge-success px-3 py-2" title="Submitted: ' . $date->format('M j, Y') . '" data-toggle="tooltip">
                        <i class="fas fa-check"></i> Submitted
                    </span>';
        }
        return '<span class="badge badge-warning px-3 py-2"><i class="fas fa-clock"></i> Pending</span>';
    }

    /**
     * Render quoted price
     */
    private function renderQuotedPrice(HB837 $hb837): string
    {
        if ($hb837->quoted_price) {
            return '<span class="font-weight-bold text-success">$' . number_format($hb837->quoted_price, 2) . '</span>';
        }
        return '<span class="text-muted">Not quoted</span>';
    }

    /**
     * Render days until renewal
     */
    private function renderDaysUntilRenewal(HB837 $hb837): string
    {
        if (!$hb837->scheduled_date_of_inspection) {
            return '<span class="text-muted">No inspection date</span>';
        }

        $inspectionDate = \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection);
        $renewalDate = $inspectionDate->copy()->addYears(3);
        $today = \Carbon\Carbon::now();
        $daysUntilRenewal = $today->diffInDays($renewalDate, false);

        $daysText = number_format($daysUntilRenewal, 2);

        if ($daysUntilRenewal < 0) {
            $badgeClass = 'badge-danger';
            $icon = 'fas fa-exclamation-triangle';
            $text = 'Overdue (' . abs($daysText) . ' days)';
        } else {
            $badgeClass = 'badge-success';
            $icon = 'fas fa-check';
            $text = $daysText . ' days';
        }

        $renewalDateFormatted = $renewalDate->format('M j, Y');

        return '<span class="badge ' . $badgeClass . ' px-2 py-1" title="Renewal due: ' . $renewalDateFormatted . '" data-toggle="tooltip">' .
            '<i class="' . $icon . '"></i> ' . $text . '</span>';
    }

    /**
     * Render billing request status
     */
    private function renderBillingRequest(HB837 $hb837): string
    {
        if ($hb837->billing_req_submitted) {
            $date = \Carbon\Carbon::parse($hb837->billing_req_submitted);
            return '<span class="badge badge-success px-3 py-2" title="Submitted: ' . $date->format('M j, Y') . '" data-toggle="tooltip">
                        <i class="fas fa-check-circle"></i> Submitted
                    </span>';
        }
        return '<span class="badge badge-secondary px-3 py-2"><i class="fas fa-clock"></i> Not Submitted</span>';
    }

    /**
     * Get 30-day overdue flag for JavaScript row styling
     */
    private function getThirtyDayOverdueFlag(HB837 $hb837): bool
    {
        $daysSinceCreated = now()->diffInDays($hb837->created_at);
        $isIncomplete = !in_array($hb837->report_status, ['completed']);
        return $isIncomplete && $daysSinceCreated > 30;
    }

    /**
     * Get columns that should be rendered as raw HTML
     */
    private function getRawColumns(): array
    {
        return [
            'checkbox',
            'overdue_status_badge',
            'property_name',
            'report_status',
            'assigned_consultant_id',
            'scheduled_date_of_inspection',
            'type_unit_type',
            'macro_client',
            'securitygauge_crime_risk',
            'contracting_status',
            'agreement_submitted',
            'quoted_price',
            'days_until_renewal',
            'billing_req_submitted',
            'action'
        ];
    }

    /**
     * Custom ordering for consultant column
     */
    private function orderByConsultant($query, string $order)
    {
        return $query->leftJoin('consultants as consultant_sort', 'hb837.assigned_consultant_id', '=', 'consultant_sort.id')
            ->orderByRaw("CASE WHEN consultant_sort.id IS NULL THEN 1 ELSE 0 END " . $order)
            ->orderByRaw("CONCAT(consultant_sort.first_name, ' ', consultant_sort.last_name) " . $order)
            ->select('hb837.*');
    }

    /**
     * Search records with query string
     */
    public function searchRecords(string $query): \Illuminate\Http\JsonResponse
    {
        $results = HB837::query()
            ->with(['consultant'])
            ->where(function ($q) use ($query) {
                $q->where('property_name', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('county', 'like', "%{$query}%")
                  ->orWhere('macro_client', 'like', "%{$query}%")
                  ->orWhereHas('consultant', function ($sq) use ($query) {
                      $sq->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'property_name' => $record->property_name,
                    'address' => $record->address,
                    'county' => $record->county,
                    'consultant' => $record->consultant->name ?? 'Unassigned',
                    'status' => $record->report_status
                ];
            });

        return response()->json($results);
    }

    /**
     * Get column visibility settings for user
     */
    public function getColumnSettings(): array
    {
        $userId = Auth::id();
        $settings = Cache::get("user_{$userId}_hb837_columns", []);
        
        return array_merge([
            'property_name' => true,
            'county' => true,
            'macro_client' => true,
            'consultant' => true,
            'scheduled_date' => true,
            'status' => true,
            'created_at' => false,
            'actions' => true
        ], $settings);
    }

    /**
     * Save column visibility settings for user
     */
    public function saveColumnSettings(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id();
        $settings = $request->input('columns', []);
        
        Cache::put("user_{$userId}_hb837_columns", $settings, 2592000); // 30 days
        
        return response()->json(['success' => true]);
    }
}
