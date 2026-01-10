<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Models\HB837;
use App\Models\Client;
use App\Models\HB837File;
use App\Models\Consultant;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Exports\HB837Export;
use App\Imports\HB837Import;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Concerns\FromArray;
use Exception;

class HB837Controller extends Controller
{
    /**
     * Display HB837 index with tabs and DataTables
     */
    public function index(Request $request, $tab = 'active')
    {
        $tab = in_array($tab = Str::lower($tab), ['all', 'active', 'quoted', 'completed', 'closed']) ? $tab : 'active';

        if ($request->ajax()) {
            return $this->getDatatablesData($tab);
        }

        // Calculate statistics for dashboard cards
        $stats = [
            'active' => HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
                ->where('contracting_status', 'executed')->count(),
            'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
            'total' => HB837::count()
        ];

        // Calculate overdue statistics (Task 18 Enhancement)
        $stats['overdue'] = HB837::whereNotNull('scheduled_date_of_inspection')
            ->where('scheduled_date_of_inspection', '<', now())
            ->where('report_status', '!=', 'completed')
            ->count();

        // Calculate 30-day overdue statistics (Task 18 Enhancement)
        $thirtyDaysAgo = now()->subDays(30);
        $stats['thirty_day_overdue'] = HB837::where('created_at', '<', $thirtyDaysAgo)
            ->whereNotIn('report_status', ['completed'])
            ->count();

        // Calculate detailed tab counts for navigation
        $tabCounts = [
            'all' => $stats['total'],
            'active' => $stats['active'],
            'quoted' => $stats['quoted'],
            'completed' => $stats['completed'],
            'closed' => $stats['closed']
        ];

        // Calculate Warning metrics (all active projects regardless of contracting status)
        $warnings = Cache::remember('hb837_warnings', 60, function () {
            return [
                'unassigned_projects' => HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
                    ->where('contracting_status', 'executed')
                    ->whereNull('assigned_consultant_id')
                    ->count(),
                'unscheduled_projects' => HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
                    ->where('contracting_status', 'executed')
                    ->whereNull('scheduled_date_of_inspection')
                    ->count(),
                'late_reports' => HB837::where('report_status', 'not-started')
                    ->where('contracting_status', 'executed')
                    ->whereNotNull('scheduled_date_of_inspection')
                    ->where('scheduled_date_of_inspection', '<', now()->subDays(30))
                    ->count()
            ];
        });

        // Calculate Current Business metrics (differentiate by contracting status)
        $allActiveProjects = HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
            ->where('contracting_status', 'executed');
        $executedProjects = HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
            ->where('contracting_status', 'executed');

        // Calculate net profit with estimation for missing data
        $actualNetProfit = $executedProjects->sum('project_net_profit') ?? 0;
        $billingWithoutProfit = HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
            ->where('contracting_status', 'executed')
            ->whereNull('project_net_profit')
            ->sum('quoted_price') ?? 0;
        $estimatedNetProfit = $billingWithoutProfit * 0.75; // 75% profit margin estimate
        $totalEstimatedNetProfit = $actualNetProfit + $estimatedNetProfit;

        $business = [
            'active_projects' => $allActiveProjects->count(),
            'gross_billing_in_process' => $executedProjects->sum('quoted_price') ?? 0,
            'net_profit_in_process' => $totalEstimatedNetProfit,
            'actual_net_profit' => $actualNetProfit,
            'estimated_net_profit' => $estimatedNetProfit
        ];

        return view('admin.hb837.index', [
            'tab' => $tab,
            'stats' => $stats,
            'tabCounts' => $tabCounts,
            'warnings' => $warnings,
            'business' => $business
        ]);
    }

    /**
     * Get DataTables data with color coding for Issue #8
     */
    private function getDatatablesData($tab)
    {
        try {
            $query = HB837::query()->with(['consultant', 'user']);

            // Apply tab filters
            $this->applyTabFilters($query, $tab);

            // Apply additional request filters (Task 18 Enhancement)
            $this->applyRequestFilters($query);

            return DataTables::of($query)
                ->addColumn('checkbox', function ($hb837) {
                    return '<input type="checkbox" class="bulk-checkbox" value="' . $hb837->id . '">';
                })
                ->addColumn('overdue_status_badge', function ($hb837) {
                    // Task 18 Enhancement: 30-day overdue status indicators
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
                })
                ->addColumn('action', function ($hb837) {
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
                })
                ->editColumn('property_name', function ($hb837) {
                    return '<strong>' . e($hb837->property_name) . '</strong><br>
                            <small class="text-muted">' . e($hb837->address) . ', ' . e($hb837->city) . ', ' . e($hb837->state) . '</small>';
                })
                ->editColumn('report_status', function ($hb837) {
                    return $this->getReportStatusCell($hb837->report_status);
                })
                ->editColumn('assigned_consultant_id', function ($hb837) {
                    return $hb837->consultant ?
                        $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name :
                        '<span class="text-muted">Unassigned</span>';
                })
                ->editColumn('scheduled_date_of_inspection', function ($hb837) {
                    if ($hb837->scheduled_date_of_inspection) {
                        $date = \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection);

                        // Apply red bold styling for "Late Reports" criteria:
                        // - Report status is "not-started"
                        // - Contracting status is "executed" 
                        // - Scheduled date is more than 30 days ago
                        $isLateReport = $hb837->report_status === 'not-started'
                            && $hb837->contracting_status === 'executed'
                            && $date->lt(now()->subDays(30));

                        $class = $isLateReport ? 'text-danger font-weight-bold' : '';
                        return '<span class="' . $class . '">' . $date->format('M j, Y') . '</span>';
                    }
                    return '<span class="text-muted">Not scheduled</span>';
                })
                ->editColumn('county', function ($hb837) {
                    return $hb837->county ?: '<span class="text-muted">Not specified</span>';
                })
                ->addColumn('type_unit_type', function ($hb837) {
                    $typeText = $hb837->property_type ?: 'Unknown Type';
                    // Capitalize the first letter of the property type
                    $typeText = ucfirst($typeText);
                    $unitsText = $hb837->units ? $hb837->units . ' units' : 'No units';
                    return e($typeText) . '<br><small class="text-muted">' . e($unitsText) . '</small>';
                })
                ->editColumn('macro_client', function ($hb837) {
                    return $hb837->macro_client ?: '<span class="text-muted">Not assigned</span>';
                })
                ->editColumn('securitygauge_crime_risk', function ($hb837) {
                    return $this->getCrimeRiskCell($hb837->securitygauge_crime_risk);
                })
                ->editColumn('contracting_status', function ($hb837) {
                    return $this->getContractingStatusCell($hb837->contracting_status);
                })
                ->editColumn('agreement_submitted', function ($hb837) {
                    return $this->getAgreementSubmittedCell($hb837->agreement_submitted);
                })
                ->editColumn('quoted_price', function ($hb837) {
                    if ($hb837->quoted_price) {
                        return '<span class="font-weight-bold text-success">$' . number_format($hb837->quoted_price, 2) . '</span>';
                    }
                    return '<span class="text-muted">Not quoted</span>';
                })
                ->addColumn('days_until_renewal', function ($hb837) {
                    return $this->getDaysUntilRenewalCell($hb837->scheduled_date_of_inspection);
                })
                ->addColumn('is_thirty_day_overdue', function ($hb837) {
                    // Task 18 Enhancement: Data for JavaScript row styling
                    $daysSinceCreated = now()->diffInDays($hb837->created_at);
                    $isIncomplete = !in_array($hb837->report_status, ['completed']);
                    return $isIncomplete && $daysSinceCreated > 30;
                })
                ->addColumn('is_overdue', function ($hb837) {
                    return $hb837->is_overdue;
                })
                ->addColumn('days_since_created', function ($hb837) {
                    return now()->diffInDays($hb837->created_at);
                })
                ->editColumn('billing_req_submitted', function ($hb837) {
                    return $this->getBillingRequestCell($hb837->billing_req_submitted);
                })
                ->rawColumns([
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
                ])
                ->orderColumn('assigned_consultant_id', function ($query, $order) {
                    // Custom ordering for consultant column - use JOIN with explicit table aliases
                    return $query->leftJoin('consultants as consultant_sort', 'hb837.assigned_consultant_id', '=', 'consultant_sort.id')
                        ->orderByRaw("CASE WHEN consultant_sort.id IS NULL THEN 1 ELSE 0 END " . $order)
                        ->orderByRaw("CONCAT(consultant_sort.first_name, ' ', consultant_sort.last_name) " . $order)
                        ->select('hb837.*'); // Explicitly select only hb837 columns to avoid conflicts
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables getDatatablesData error for tab ' . $tab . ': ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Return empty DataTables response structure
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
     * Get color-coded crime risk cell per GitHub Issue #8
     */
    private function getCrimeRiskCell($risk)
    {
        if (!$risk)
            return '<span class="text-muted" style="font-size: 14px; font-weight: 500;">Not Assessed</span>';

        $cssClasses = [
            'Low' => 'risk-low',
            'Moderate' => 'risk-moderate',
            'Elevated' => 'risk-elevated',
            'High' => 'risk-high',
            'Severe' => 'risk-severe'
        ];

        $class = isset($cssClasses[$risk]) ? $cssClasses[$risk] : '';

        return '<span class="badge px-3 py-2 ' . $class . '" style="font-size: 13px; font-weight: 500;">' . e($risk) . '</span>';
    }

    /**
     * Get color-coded report status cell per GitHub Issue #8
     */
    private function getReportStatusCell($status)
    {
        if (!$status) return '<span class="text-muted">No status</span>';

        $colors = [
            'not-started' => ['bg' => '#f8d7da', 'text' => '#721c24'],
            'underway' => ['bg' => '#fff3cd', 'text' => '#856404'],
            'in-review' => ['bg' => '#cce5ff', 'text' => '#004085'],
            'completed' => ['bg' => '#d4edda', 'text' => '#155724']
        ];

        $style = isset($colors[$status]) ?
            'background-color: ' . $colors[$status]['bg'] . '; color: ' . $colors[$status]['text'] . ';' : '';

        $displayStatus = ucfirst(str_replace('-', ' ', $status));
        return '<span class="badge px-3 py-2" style="' . $style . '">' . e($displayStatus) . '</span>';
    }

    /**
     * Get color-coded contracting status cell
     */
    private function getContractingStatusCell($status)
    {
        if (!$status) return '<span class="text-muted">No status</span>';

        $colors = [
            'quoted' => ['bg' => '#e3f2fd', 'text' => '#0d47a1'],
            'started' => ['bg' => '#fff8e1', 'text' => '#e65100'],
            'executed' => ['bg' => '#e8f5e8', 'text' => '#2e7d32'],
            'closed' => ['bg' => '#fce4ec', 'text' => '#c2185b']
        ];

        $style = isset($colors[$status]) ?
            'background-color: ' . $colors[$status]['bg'] . '; color: ' . $colors[$status]['text'] . ';' : '';

        $displayStatus = ucfirst(str_replace('-', ' ', $status));
        return '<span class="badge px-3 py-2" style="' . $style . '">' . e($displayStatus) . '</span>';
    }

    /**
     * Get agreement submitted status cell
     */
    private function getAgreementSubmittedCell($submitted)
    {
        if ($submitted) {
            $date = \Carbon\Carbon::parse($submitted);
            return '<span class="badge badge-success px-3 py-2" title="Submitted: ' . $date->format('M j, Y') . '" data-toggle="tooltip">
                        <i class="fas fa-check"></i> Submitted
                    </span>';
        }
        return '<span class="badge badge-warning px-3 py-2"><i class="fas fa-clock"></i> Pending</span>';
    }

    /**
     * Get billing request submitted status cell
     */
    private function getBillingRequestCell($submitted)
    {
        if ($submitted) {
            $date = \Carbon\Carbon::parse($submitted);
            return '<span class="badge badge-success px-3 py-2" title="Submitted: ' . $date->format('M j, Y') . '" data-toggle="tooltip">
                        <i class="fas fa-check-circle"></i> Submitted
                    </span>';
        }
        return '<span class="badge badge-secondary px-3 py-2"><i class="fas fa-clock"></i> Not Submitted</span>';
    }

    /**
     * Calculate days until renewal (3 years from scheduled inspection date)
     */
    private function getDaysUntilRenewalCell($scheduledDate)
    {
        if (!$scheduledDate) {
            return '<span class="text-muted">No inspection date</span>';
        }

        $inspectionDate = \Carbon\Carbon::parse($scheduledDate);
        $renewalDate = $inspectionDate->copy()->addYears(3);
        $today = \Carbon\Carbon::now();
        $daysUntilRenewal = $today->diffInDays($renewalDate, false);

        // Show only 2 decimal places
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
     * Calculate priority score based on various factors
     */
    private function calculatePriorityScore($hb837)
    {
        $score = 0;

        // Overdue inspection
        if ($hb837->scheduled_date_of_inspection &&
            \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection)->isPast() &&
            $hb837->report_status !== 'completed') {
            $score += 2;
        }

        // High crime risk
        if (in_array($hb837->securitygauge_crime_risk, ['High', 'Severe'])) {
            $score += 1;
        }

        // No assigned consultant
        if (!$hb837->assigned_consultant_id) {
            $score += 1;
        }

        // High value property
        if ($hb837->quoted_price && $hb837->quoted_price > 10000) {
            $score += 1;
        }

        return min(3, $score); // Cap at 3 (Urgent)
    }

    /**
     * Apply tab filters to query
     */
    protected function applyTabFilters($query, $tab)
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
    protected function applyRequestFilters($query)
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
     * Show the form for creating a new HB837 record
     */
    public function create()
    {
        $consultants = Consultant::all();
        $propertyTypes = config('hb837.property_types');
        $securityGauge = config('hb837.security_gauge');

        return view('admin.hb837.create', compact('consultants', 'propertyTypes', 'securityGauge'));
    }

    /**
     * Store a newly created HB837 record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_name' => 'required|string|max:255',
            'management_company' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'property_type' => 'nullable|string|max:255',
            'units' => 'nullable|integer|min:1',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'assigned_consultant_id' => 'nullable|integer|exists:consultants,id',
            'scheduled_date_of_inspection' => 'nullable|date',
            'report_status' => 'nullable|in:not-started,underway,in-review,completed',
            'contracting_status' => 'nullable|in:quoted,started,executed,closed',
            'quoted_price' => 'nullable|numeric',
            'sub_fees_estimated_expenses' => 'nullable|numeric',
            'billing_req_sent' => 'nullable|date',
            'billing_req_submitted' => 'nullable|date',
            'report_submitted' => 'nullable|date',
            'agreement_submitted' => 'nullable|date',
            'project_net_profit' => 'nullable|numeric',
            'securitygauge_crime_risk' => 'nullable|string|max:255',
            'macro_client' => 'nullable|string|max:255',
            'macro_contact' => 'nullable|string|max:255',
            'macro_email' => 'nullable|email|max:255',
            'property_manager_name' => 'nullable|string|max:255',
            'property_manager_email' => 'nullable|email|max:255',
            'regional_manager_name' => 'nullable|string|max:255',
            'regional_manager_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'consultant_notes' => 'nullable|string'
        ]);

        // Set user_id to current authenticated user
        $validated['user_id'] = \Illuminate\Support\Facades\Auth::id();

        // Consultant select workaround
        if (($validated['assigned_consultant_id'] ?? null) == -1) {
            $validated['assigned_consultant_id'] = null;
        }

        // Calculate net profit
        if (isset($validated['quoted_price']) && isset($validated['sub_fees_estimated_expenses'])) {
            $validated['project_net_profit'] = $validated['quoted_price'] - $validated['sub_fees_estimated_expenses'];
        }

        $hb837 = HB837::create($validated);

        return redirect()->route('admin.hb837.edit', ['hb837' => $hb837->id])
            ->with('success', 'HB837 record created successfully!');
    }

    /**
     * Display the specified HB837 record
     */
    public function show(HB837 $hb837)
    {
        $hb837->load(['consultant', 'user', 'files']);

        return view('admin.hb837.show', compact('hb837'));
    }

    /**
     * Generate PDF report for a specific HB837 record
     */
    public function generatePdfReport(HB837 $hb837)
    {
        $hb837->load(['consultant', 'user', 'files']);

        // Prepare Google Maps data
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $hasValidAddress = !empty($hb837->address);
        $hasApiKey = !empty($apiKey);

        $mapUrl = null;
        $showMap = false;
        $fallbackReason = 'No address available';

        if ($hasValidAddress && $hasApiKey) {
            // Build the full address for better geocoding
            $fullAddress = trim(implode(', ', array_filter([
                $hb837->address,
                $hb837->city,
                $hb837->state,
                $hb837->zip
            ])));

            // Generate Google Maps Static API URL
            $mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?' . http_build_query([
                'center' => $fullAddress,
                'zoom' => 15,
                'size' => '600x400',
                'maptype' => 'roadmap',
                'markers' => 'color:red|label:P|' . $fullAddress,
                'key' => $apiKey,
                'format' => 'png'
            ]);

            $showMap = true;
            $fallbackReason = null;
        } elseif (!$hasValidAddress) {
            $fallbackReason = 'No address available';
        } elseif (!$hasApiKey) {
            $fallbackReason = 'Google Maps API key not configured';
        }

        // Prepare data for PDF
        $data = [
            'hb837' => $hb837,
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
            'generated_by' => Auth::user()->name ?? 'System',
            'map_url' => $mapUrl,
            'show_map' => $showMap,
            'map_fallback_reason' => $fallbackReason
        ];

        // Generate PDF using the view
        $pdf = Pdf::loadView('admin.hb837.pdf-report', $data);

        // Set PDF options
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Generate filename with property name using helper method
        $suffix = 'ID' . $hb837->id . '_' . date('Y-m-d');
        $filename = 'HB837_' . $this->createCleanFilename($hb837->property_name, $suffix);

        // Return PDF download
        return $pdf->download($filename);
    }

    /**
     * Create a clean filename from property name
     * Removes special characters and limits length for filesystem compatibility
     */
    private function createCleanFilename($propertyName, $suffix = '', $extension = '.pdf')
    {
        // Default fallback if no property name
        if (!$propertyName) {
            $propertyName = 'Unknown_Property';
        }

        // Clean property name for filename (remove special characters, spaces, etc.)
        $cleanName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $propertyName);
        $cleanName = preg_replace('/_{2,}/', '_', $cleanName); // Replace multiple underscores with single
        $cleanName = trim($cleanName, '_'); // Remove leading/trailing underscores

        // Limit length to avoid filesystem issues
        if (strlen($cleanName) > 50) {
            $cleanName = substr($cleanName, 0, 50);
            $cleanName = rtrim($cleanName, '_'); // Remove trailing underscore if substr cut in middle
        }

        // Add suffix if provided
        if ($suffix) {
            $cleanName .= '_' . $suffix;
        }

        return $cleanName . $extension;
    }

    /**
     * Show the form for editing the specified HB837 record
     */
    public function edit(HB837 $hb837, $tab = 'general')
    {
        $tab = in_array($tab, ['general', 'address', 'contact', 'financial', 'notes', 'files', 'maps']) ? $tab : 'general';

        $consultants = Consultant::all();
        $propertyTypes = config('hb837.property_types');
        $securityGauge = config('hb837.security_gauge');

        $hb837->load(['consultant', 'user', 'files']);

        return view('admin.hb837.edit', compact('hb837', 'tab', 'consultants', 'propertyTypes', 'securityGauge'));
    }

    /**
     * Update the specified HB837 record
     */
    public function update(Request $request, HB837 $hb837, $tabId = 'general')
    {
        $validated = $request->validate([
            'property_name' => 'required|string|max:255',
            'management_company' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'property_type' => 'nullable|string|max:255',
            'units' => 'nullable|integer|min:1',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'assigned_consultant_id' => 'nullable|integer|exists:consultants,id',
            'scheduled_date_of_inspection' => 'nullable|date',
            'report_status' => 'nullable|in:not-started,underway,in-review,completed',
            'contracting_status' => 'nullable|in:quoted,started,executed,closed',
            'quoted_price' => 'nullable|numeric',
            'sub_fees_estimated_expenses' => 'nullable|numeric',
            'billing_req_sent' => 'nullable|date',
            'billing_req_submitted' => 'nullable|date',
            'report_submitted' => 'nullable|date',
            'agreement_submitted' => 'nullable|date',
            'project_net_profit' => 'nullable|numeric',
            'securitygauge_crime_risk' => 'nullable|string|max:255',
            'macro_client' => 'nullable|string|max:255',
            'macro_contact' => 'nullable|string|max:255',
            'macro_email' => 'nullable|email|max:255',
            'property_manager_name' => 'nullable|string|max:255',
            'property_manager_email' => 'nullable|email|max:255',
            'regional_manager_name' => 'nullable|string|max:255',
            'regional_manager_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'financial_notes' => 'nullable|string',
            'consultant_notes' => 'nullable|string'
        ]);

        // Calculate net profit
        if (isset($validated['quoted_price']) && isset($validated['sub_fees_estimated_expenses'])) {
            $validated['project_net_profit'] = $validated['quoted_price'] - $validated['sub_fees_estimated_expenses'];
        }

        // Explicitly assign date fields if present to ensure they update
        foreach (['scheduled_date_of_inspection', 'billing_req_submitted', 'report_submitted', 'agreement_submitted'] as $dateField) {
            if (array_key_exists($dateField, $validated)) {
                $hb837->$dateField = $validated[$dateField];
            }
        }



        $hb837->fill($validated);
        $hb837->save();

        return redirect()->route('admin.hb837.edit', ['hb837' => $hb837->getKey(), 'tab' => $tabId])
            ->with('success', 'HB837 record updated successfully!');
    }

    /**
     * Remove the specified HB837 record
     */
    public function destroy(HB837 $hb837)
    {
        $hb837->delete();

        return redirect()->route('admin.hb837.index')
            ->with('success', 'HB837 record deleted successfully!');
    }

    /**
     * Export HB837 data to Excel
     */
    public function export(Request $request)
    {
        $filename = 'hb837_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        try {
            return Excel::download(new HB837Export($request->get('tab', 'active')), $filename);
        } catch (\Exception $e) {
            Log::error('HB837 Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Import HB837 data from Excel with three-phase workflow support
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'import_phase' => 'required|in:initial,update,review',
            'action' => 'required|in:preview,import'
        ]);

        try {
            $import = new HB837Import();
            $import->setPhase($request->get('import_phase'));

            if ($request->get('action') === 'preview') {
                // Preview mode - analyze what would change
                $comparison = $import->compare($request->file('file'), $request->get('import_phase'));

                return redirect()->back()->with('preview_data', $comparison);
            } else {
                // Actually perform the import
                Excel::import($import, $request->file('file'));

                $message = sprintf(
                    'Phase "%s" import completed: %d imported, %d updated, %d skipped',
                    ucfirst($request->get('import_phase')),
                    $import->importedCount,
                    $import->updatedCount,
                    $import->skippedCount
                );

                return redirect()->back()->with('success', $message);
            }

        } catch (\Exception $e) {
            Log::error('HB837 Three-Phase Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute three-phase import workflow
     */
    public function executeThreePhaseImport(Request $request)
    {
        $request->validate([
            'file_phase1' => 'required|file|mimes:xlsx,xls,csv',
            'file_phase2' => 'required|file|mimes:xlsx,xls,csv',
            'file_phase3' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new HB837Import();

            // Store files temporarily
            $file1Path = $request->file('file_phase1')->store('temp');
            $file2Path = $request->file('file_phase2')->store('temp');
            $file3Path = $request->file('file_phase3')->store('temp');

            $results = $import->executeThreePhaseImport(
                storage_path('app/' . $file1Path),
                storage_path('app/' . $file2Path),
                storage_path('app/' . $file3Path)
            );

            // Clean up temporary files
            Storage::delete([$file1Path, $file2Path, $file3Path]);

            return redirect()->back()->with('three_phase_results', $results);

        } catch (\Exception $e) {
            Log::error('Three-Phase Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Three-phase import failed: ' . $e->getMessage());
        }
    }

    /**
     * Compare import file with existing data
     */
    public function compareImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'import_phase' => 'required|in:initial,update,review'
        ]);

        try {
            $import = new HB837Import();
            $comparison = $import->compare($request->file('file'), $request->get('import_phase'));

            return response()->json([
                'success' => true,
                'comparison' => $comparison
            ]);

        } catch (\Exception $e) {
            Log::error('Import Comparison Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show three-phase import form
     */
    public function showThreePhaseImport()
    {
        return view('admin.hb837.three-phase-import');
    }

    /**
     * Redirect to new 3-phase import system
     */
    public function newImport()
    {
        return redirect()->route('modules.hb837.import.index');
    }

    /**
     * Redirect to module dashboard
     */
    public function moduleDashboard()
    {
        return redirect()->route('modules.hb837.index');
    }

    /**
     * Get sortable columns for DataTables
     */
    protected function sortableColumns()
    {
        return [
            'created_at',
            'updated_at',
            'property_name',
            'county',
            'macro_client',
            'assigned_consultant_id',
            'scheduled_date_of_inspection',
            'report_status',
            'property_type',
            'units',
            'management_company',
            'agreement_submitted',
            'contracting_status',
            'billing_req_sent',
            'billing_req_submitted',
            'report_submitted',
            'securitygauge_crime_risk'
        ];
    }

    /**
     * Get data for DataTables AJAX - alternative endpoint
     */
    public function getData(Request $request)
    {
        $tab = $request->get('tab', 'active');
        return $this->getDatatablesData($tab);
    }

    /**
     * Get DataTables data for specific tab (AJAX endpoint)
     */
    public function getTabData(Request $request, $tab = 'active')
    {
        // Allow both AJAX and direct requests for debugging
        return $this->getDatatablesData($tab);
    }

    /**
     * Perform bulk actions on multiple HB837 records
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,status_update,consultant_assign',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'integer|exists:hb837,id',
            'bulk_status' => 'nullable|in:not-started,underway,in-review,completed',
            'bulk_consultant_id' => 'nullable|integer|exists:consultants,id'
        ]);

        $records = HB837::whereIn('id', $request->selected_ids);
        $count = $records->count();

        switch ($request->action) {
            case 'delete':
                $records->delete();
                return response()->json([
                    'success' => true,
                    'message' => "{$count} records deleted successfully."
                ]);

            case 'status_update':
                $records->update(['report_status' => $request->bulk_status]);
                return response()->json([
                    'success' => true,
                    'message' => "{$count} records updated to " . ucfirst(str_replace('-', ' ', $request->bulk_status)) . " status."
                ]);

            case 'consultant_assign':
                $records->update(['assigned_consultant_id' => $request->bulk_consultant_id]);
                $consultant = $request->bulk_consultant_id ?
                    Consultant::find($request->bulk_consultant_id)->first_name . ' ' . Consultant::find($request->bulk_consultant_id)->last_name :
                    'Unassigned';
                return response()->json([
                    'success' => true,
                    'message' => "{$count} records assigned to {$consultant}."
                ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.']);
    }

    /**
     * Quick status update for a single record
     */
    public function updateStatus(Request $request, HB837 $hb837)
    {
        $request->validate([
            'status' => 'required|in:not-started,underway,in-review,completed'
        ]);

        $hb837->update(['report_status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated to ' . ucfirst(str_replace('-', ' ', $request->status)) . '.'
        ]);
    }

    /**
     * Quick priority update for a single record
     */
    public function updatePriority(Request $request, HB837 $hb837)
    {
        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent'
        ]);

        $hb837->update(['priority' => $request->priority]);

        return response()->json([
            'success' => true,
            'message' => 'Priority updated to ' . ucfirst($request->priority) . '.'
        ]);
    }

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('admin.hb837.import');
    }

    /**
     * Process import file
     */
    public function processImport(Request $request)
    {
        // This will call the import method defined earlier
        return $this->import($request);
    }

    /**
     * Export in specific format
     */
    public function exportFormat(Request $request, $format = 'xlsx')
    {
        $filename = 'hb837_export_' . now()->format('Y-m-d_H-i-s') . '.' . $format;

        try {
            // Handle PDF export differently
            if ($format === 'pdf') {
                return $this->exportBulkPdf($request);
            }

            return Excel::download(new HB837Export($request->get('tab', 'active'), $format), $filename);
        } catch (\Exception $e) {
            Log::error('HB837 Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export bulk PDF report
     */
    private function exportBulkPdf(Request $request)
    {
        $tab = $request->get('tab', 'active');
        $search = $request->get('search', '');

        // Get filtered records
        $query = HB837::query()->with(['consultant', 'user']);

        // Apply tab filters
        switch ($tab) {
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

        // Apply search filter if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('property_name', 'ilike', '%' . $search . '%')
                    ->orWhere('address', 'ilike', '%' . $search . '%')
                    ->orWhere('city', 'ilike', '%' . $search . '%')
                    ->orWhere('management_company', 'ilike', '%' . $search . '%');
            });
        }

        $records = $query->orderBy('property_name')->get();

        // Prepare data for PDF
        $data = [
            'records' => $records,
            'tab' => $tab,
            'search' => $search,
            'total_count' => $records->count(),
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
            'generated_by' => Auth::user()->name ?? 'System',
            'tab_title' => ucfirst(str_replace('-', ' ', $tab)) . ' Projects'
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.hb837.bulk-pdf-report', $data);
        $pdf->setPaper('letter', 'landscape'); // Landscape for better table layout
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        $filename = 'HB837_Bulk_Report_' . ucfirst($tab) . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * List files for a specific HB837 record
     */
    public function files(HB837 $hb837)
    {
        $hb837->load('files');
        return view('admin.hb837.files', compact('hb837'));
    }

    /**
     * Upload file for a specific HB837 record
     */
    public function uploadFile(Request $request, HB837 $hb837)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('hb837/' . $hb837->id, $filename, 'public');

            HB837File::create([
                'hb837_id' => $hb837->id,
                'filename' => $filename,                           // stored filename with timestamp
                'original_filename' => $file->getClientOriginalName(), // original filename from user
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'description' => $request->description,
                'uploaded_by' => \Illuminate\Support\Facades\Auth::id()
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('HB837 File Upload Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'File upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a specific file
     */
    public function downloadFile(HB837File $file)
    {
        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $fullPath = Storage::disk('public')->path($file->file_path);
        return Response::download($fullPath, $file->original_filename);
    }

    /**
     * Delete a specific file
     */
    public function deleteFile(HB837File $file)
    {
        try {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 File Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'File deletion failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX search for HB837 records
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = HB837::where('property_name', 'ILIKE', "%{$query}%")
            ->orWhere('address', 'ILIKE', "%{$query}%")
            ->orWhere('city', 'ILIKE', "%{$query}%")
            ->orWhere('county', 'ILIKE', "%{$query}%")
            ->orWhere('macro_client', 'ILIKE', "%{$query}%")
            ->with(['consultant'])
            ->take(10)
            ->get()
            ->map(function ($hb837) {
                return [
                    'id' => $hb837->id,
                    'text' => $hb837->property_name . ' (' . $hb837->address . ')',
                    'property_name' => $hb837->property_name,
                    'address' => $hb837->address,
                    'consultant' => $hb837->consultant ?
                        $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name :
                        'Unassigned',
                    'status' => $hb837->report_status
                ];
            });

        return response()->json($results);
    }

    /**
     * Duplicate an existing HB837 record
     */
    public function duplicate(HB837 $hb837)
    {
        try {
            $data = $hb837->toArray();

            // Remove fields that shouldn't be duplicated
            unset($data['id'], $data['created_at'], $data['updated_at']);

            // Modify the property name to indicate it's a duplicate
            $data['property_name'] = $data['property_name'] . ' (Copy)';

            // Reset status fields for the duplicate
            $data['report_status'] = 'not-started';
            $data['scheduled_date_of_inspection'] = null;
            $data['billing_req_sent'] = null;
            $data['report_submitted'] = null;
            $data['agreement_submitted'] = null;

            $duplicate = HB837::create($data);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Record duplicated successfully!',
                    'duplicate_id' => $duplicate->id
                ]);
            }

            return redirect()->route('admin.hb837.edit', ['hb837' => $duplicate->id])
                ->with('success', 'Record duplicated successfully!');

        } catch (\Exception $e) {
            Log::error('HB837 Duplicate Error: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Duplication failed: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'Duplication failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle AJAX delete request for DataTables
     */
    public function ajaxDestroy(HB837 $hb837)
    {
        try {
            $hb837->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('HB837 AJAX Delete Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get updated statistics for dashboard cards and tab counts (AJAX endpoint)
     */
    public function getStats()
    {
        // Basic statistics
        $stats = [
            'active' => HB837::whereIn('report_status', ['not-started', 'underway', 'in-review'])
                ->where('contracting_status', 'executed')->count(),
            'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
            'total' => HB837::count()
        ];

        // Calculate overdue statistics
        $stats['overdue'] = HB837::whereNotNull('scheduled_date_of_inspection')
            ->where('scheduled_date_of_inspection', '<', now())
            ->where('report_status', '!=', 'completed')
            ->count();

        // Calculate 30-day overdue statistics (Task 18 Enhancement)
        $thirtyDaysAgo = now()->subDays(30);
        $stats['thirty_day_overdue'] = HB837::where('created_at', '<', $thirtyDaysAgo)
            ->whereNotIn('report_status', ['completed'])
            ->count();

        // Calculate tab counts for navigation
        $tabCounts = [
            'all' => $stats['total'],
            'active' => $stats['active'],
            'quoted' => $stats['quoted'],
            'completed' => $stats['completed'],
            'closed' => $stats['closed']
        ];

        // Add detailed status breakdown
        $statusCounts = [
            'not_started' => HB837::where('report_status', 'not-started')->count(),
            'in_progress' => HB837::where('report_status', 'underway')->count(),
            'in_review' => HB837::where('report_status', 'in-review')->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
        ];

        // Add contracting status breakdown
        $contractingCounts = [
            'quoted' => HB837::where('contracting_status', 'quoted')->count(),
            'started' => HB837::where('contracting_status', 'started')->count(),
            'executed' => HB837::where('contracting_status', 'executed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
        ];

        return response()->json([
            'stats' => $stats,
            'tabCounts' => $tabCounts,
            'statusCounts' => $statusCounts,
            'contractingCounts' => $contractingCounts
        ]);
    }

    /**
     * Show smart import interface
     */
    public function showSmartImport()
    {
        return view('admin.hb837.smart-import');
    }

    /**
     * Analyze uploaded file intelligently
     */
    public function analyzeImportFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();            // Ensure temp/imports directory exists with proper recursive creation
            $tempDirPath = 'temp' . DIRECTORY_SEPARATOR . 'imports';
            $fullTempDir = storage_path('app' . DIRECTORY_SEPARATOR . $tempDirPath);

            if (!file_exists($fullTempDir)) {
                if (!mkdir($fullTempDir, 0755, true)) {
                    throw new \Exception("Failed to create upload directory: {$fullTempDir}");
                }
            }

            // Verify directory is writable
            if (!is_writable($fullTempDir)) {
                throw new \Exception("Upload directory is not writable: {$fullTempDir}");
            }            // Store file temporarily with better error handling
            $fileName = 'import_' . time() . '_' . uniqid() . '.' . $extension;

            // Use manual file storage instead of Laravel Storage due to disk configuration
            $targetPath = $fullTempDir . DIRECTORY_SEPARATOR . $fileName;

            try {
                if (!$file->move($fullTempDir, $fileName)) {
                    throw new \Exception("File move operation failed");
                }
                $filePath = 'temp' . DIRECTORY_SEPARATOR . 'imports' . DIRECTORY_SEPARATOR . $fileName;
            } catch (\Exception $e) {
                throw new \Exception("Failed to move uploaded file: " . $e->getMessage());
            }

            // Verify file was stored successfully
            if (!file_exists($targetPath)) {
                throw new \Exception("Failed to store uploaded file at: {$targetPath}");
            }

            // Verify file is readable
            if (!is_readable($targetPath)) {
                throw new \Exception("Stored file is not readable: {$targetPath}");
            }

            // Use the target path for analysis
            $fullPath = $targetPath;

            // Log successful upload for debugging
            Log::info("File uploaded successfully", [
                'original_name' => $originalName,
                'stored_path' => $fullPath,
                'file_size' => filesize($fullPath)
            ]);

            // Analyze file structure
            $analysis = $this->performFileAnalysis($fullPath);

            // Store analysis data temporarily
            $fileId = uniqid();
            Cache::put("import_analysis_{$fileId}", [
                'file_path' => $filePath,
                'file_name' => $originalName,
                'analysis' => $analysis
            ], 3600); // Store for 1 hour

            return response()->json([
                'success' => true,
                'file_id' => $fileId,
                'detection' => $analysis['detection'],
                'stats' => $analysis['stats'],
                'mapping' => $analysis['mapping'],
                'warnings' => $analysis['warnings']
            ]);

        } catch (\Exception $e) {
            Log::error('Smart import analysis failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview import data
     */
    public function previewImportData(Request $request)
    {
        $fileId = $request->get('file_id');

        $cacheData = Cache::get("import_analysis_{$fileId}");
        if (!$cacheData) {
            return response()->json(['success' => false, 'message' => 'File analysis not found or expired'], 404);
        }

        try {
            $filePath = storage_path('app/' . $cacheData['file_path']);
            $previewData = $this->generatePreviewData($filePath, $cacheData['analysis']);

            return response()->json([
                'success' => true,
                'headers' => $previewData['headers'],
                'preview_rows' => $previewData['rows'],
                'total_rows' => $previewData['total_rows']
            ]);

        } catch (\Exception $e) {
            Log::error('Preview generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute smart import
     */
    public function executeSmartImport(Request $request)
    {
        $fileId = $request->get('file_id');

        $cacheData = Cache::get("import_analysis_{$fileId}");
        if (!$cacheData) {
            return response()->json(['success' => false, 'message' => 'File analysis not found or expired'], 404);
        }

        try {
            $filePath = storage_path('app/' . $cacheData['file_path']);
            $analysis = $cacheData['analysis'];

            $result = $this->executeIntelligentImport($filePath, $analysis);

            // Clean up temporary file
            Storage::delete($cacheData['file_path']);
            Cache::forget("import_analysis_{$fileId}");

            // Store results in session for the results page
            session([
                'import_results' => [
                    'file_name' => $cacheData['file_name'] ?? 'imported_file',
                    'imported' => $result['imported'],
                    'updated' => $result['updated'],
                    'skipped' => $result['skipped'],
                    'errors' => $result['errors'],
                    'total_processed' => $result['imported'] + $result['updated'] + $result['skipped'],
                    'analysis' => $analysis,
                    'import_timestamp' => now()
                ]
            ]);

            return response()->json([
                'success' => true,
                'imported' => $result['imported'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors'],
                'redirect_url' => route('admin.hb837.import-results')
            ]);

        } catch (\Exception $e) {
            Log::error('Smart import execution failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export template file
     */
    public function exportTemplate($format)
    {
        $headers = [
            'property_name',
            'address',
            'city',
            'county',
            'state',
            'zip',
            'phone',
            'management_company',
            'owner_name',
            'property_type',
            'units',
            'securitygauge_crime_risk',
            'macro_client',
            'macro_contact',
            'macro_email',
            'property_manager_name',
            'property_manager_email',
            'regional_manager_name',
            'regional_manager_email',
            'report_status',
            'contracting_status',
            'scheduled_date_of_inspection',
            'quoted_price'
        ];

        $filename = 'hb837_template_' . date('Y-m-d');

        if ($format === 'csv') {
            $output = fopen('php://output', 'w');

            return response()->stream(function () use ($output, $headers) {
                fputcsv($output, $headers);
                fclose($output);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ]);
        } else {
            // Excel template
            $headers = [
                'Property Name',
                'County',
                'Crime Risk',
                'Macro Client',
                'Consultant',
                'Scheduled Date',
                'Contract Status',
                'Quoted Price',
                'Priority',
                'Notes'
            ];

            return Excel::download(new class ($headers) implements FromArray {
                private $headers;

                public function __construct($headers)
                {
                    $this->headers = $headers;
                }

                public function array(): array
                {
                    return [$this->headers];
                }
            }, "{$filename}.xlsx");
        }
    }

    /**
     * Perform intelligent file analysis
     */
    private function performFileAnalysis($filePath)
    {
        // Verify file exists
        if (!file_exists($filePath)) {
            throw new \Exception("File does not exist: {$filePath}");
        }

        // Check file is readable
        if (!is_readable($filePath)) {
            throw new \Exception("File is not readable: {$filePath}");
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = [];
        $headers = [];

        try {
            // Read file based on type
            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
            } else if (strtolower($extension) === 'csv') {
                $handle = fopen($filePath, 'r');
                if ($handle === false) {
                    throw new \Exception("Failed to open CSV file: {$filePath}");
                }
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            } else {
                throw new \Exception("Unsupported file format: {$extension}");
            }
        } catch (\Exception $e) {
            throw new \Exception("Failed to read file: " . $e->getMessage());
        }

        if (empty($data)) {
            throw new \Exception('No data found in file');
        }

        // Extract headers (assume first row)
        $headers = array_shift($data);
        $headers = array_map('trim', $headers);

        // Analyze file content
        $stats = [
            'total_rows' => count(array_filter($data, function ($row) {
                return !empty(array_filter($row));
            })),
            'valid_rows' => 0,
            'columns' => count($headers),
            'new_records' => 0,
            'updates' => 0
        ];

        // Map columns to database fields
        $mapping = $this->intelligentColumnMapping($headers);

        // Detect import type
        $detection = $this->detectImportType($headers, $data);

        // Validate data and count valid rows
        $validationResults = $this->validateImportData($data, $mapping);
        $stats['valid_rows'] = $validationResults['valid_count'];
        $stats['new_records'] = $validationResults['new_count'];
        $stats['updates'] = $validationResults['update_count'];

        // Generate warnings
        $warnings = $this->generateWarnings($mapping, $validationResults);

        return [
            'detection' => $detection,
            'stats' => $stats,
            'mapping' => $mapping,
            'warnings' => $warnings,
            'headers' => $headers,
            'sample_data' => array_slice($data, 0, 5) // First 5 rows for analysis
        ];
    }

    /**
     * Intelligent column mapping using precise matching first, then fuzzy matching
     */
    private function intelligentColumnMapping($headers)
    {
        // Get field mappings from config
        $fieldMappings = config('hb837_field_mapping.field_mapping', []);

        $mappings = [];
        $usedFields = []; // Track which fields have been mapped

        foreach ($headers as $header) {
            $bestMatch = null;
            $bestScore = 0;
            $headerTrimmed = trim($header);
            $headerLower = strtolower($headerTrimmed);

            // First pass: Look for exact matches (case insensitive)
            foreach ($fieldMappings as $field => $patterns) {
                if (isset($usedFields[$field]))
                    continue;

                foreach ($patterns as $pattern) {
                    if (strcasecmp($headerTrimmed, $pattern) === 0) {
                        $bestMatch = $field;
                        $bestScore = 1.0;
                        break 2; // Perfect match found, exit both loops
                    }
                }
            }

            // Second pass: If no exact match, use fuzzy matching with higher threshold
            if (!$bestMatch) {
                foreach ($fieldMappings as $field => $patterns) {
                    // Skip field if already used with high confidence
                    if (isset($usedFields[$field]) && $usedFields[$field] > 0.9) {
                        continue;
                    }

                    foreach ($patterns as $pattern) {
                        $score = $this->calculateSimilarity($headerLower, strtolower($pattern));

                        // Use higher threshold (0.8) for fuzzy matching to reduce errors
                        if ($score >= 0.8 && $score > $bestScore) {
                            $bestScore = $score;
                            $bestMatch = $field;
                        }
                    }
                }
            }

            $mappings[] = [
                'source_column' => $header,
                'target_field' => $bestMatch ?: 'unmapped',
                'confidence' => $bestScore
            ];

            // Mark field as used if mapped with any confidence
            if ($bestMatch) {
                $usedFields[$bestMatch] = $bestScore;
            }
        }

        return $mappings;
    }

    /**
     * Calculate similarity between two strings
     */
    private function calculateSimilarity($str1, $str2)
    {
        // Use Levenshtein distance for fuzzy matching
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        if ($len1 == 0)
            return $len2 == 0 ? 1 : 0;
        if ($len2 == 0)
            return 0;

        // Check for exact match first
        if ($str1 === $str2) {
            return 1.0;
        }

        // Check for substring matches
        if (strpos($str1, $str2) !== false || strpos($str2, $str1) !== false) {
            $minLen = min($len1, $len2);
            $maxLen = max($len1, $len2);
            $similarity = 0.8 + (0.2 * ($maxLen > 0 ? $minLen / $maxLen : 0));
            return min(1, $similarity);
        }

        // Calculate Levenshtein distance
        $levenshtein = levenshtein($str1, $str2);
        $maxLen = max($len1, $len2);

        $similarity = 1 - ($levenshtein / $maxLen);

        // Check for word overlap - improved algorithm
        if ($similarity > 0.3) {
            $words1 = explode(' ', $str1);
            $words2 = explode(' ', $str2);
            $overlap = count(array_intersect($words1, $words2));
            $totalWords = count(array_unique(array_merge($words1, $words2)));

            if ($overlap > 0) {
                $wordBoost = ($overlap / $totalWords) * 0.3;
                $similarity += $wordBoost;
            }
        }

        // Set minimum threshold - anything below 0.5 should be considered low confidence
        return min(1, max(0, $similarity));
    }

    /**
     * Detect import type based on file content
     */
    private function detectImportType($headers, $data)
    {
        // Analyze headers and data to determine import type
        $hasPropertyNames = false;
        $hasAddresses = false;
        $hasContacts = false;

        foreach ($headers as $header) {
            $lower = strtolower($header);
            if (strpos($lower, 'property') !== false || strpos($lower, 'name') !== false) {
                $hasPropertyNames = true;
            }
            if (strpos($lower, 'address') !== false || strpos($lower, 'street') !== false) {
                $hasAddresses = true;
            }
            if (strpos($lower, 'email') !== false || strpos($lower, 'phone') !== false) {
                $hasContacts = true;
            }
        }

        $type = 'general';
        if ($hasPropertyNames && $hasAddresses) {
            $type = 'property_list';
        } else if ($hasContacts) {
            $type = 'contact_update';
        }

        return [
            'type' => $type,
            'confidence' => 0.8,
            'description' => $this->getImportTypeDescription($type)
        ];
    }

    /**
     * Get description for import type
     */
    private function getImportTypeDescription($type)
    {
        $descriptions = [
            'property_list' => 'Property listing with addresses and details',
            'contact_update' => 'Contact information update',
            'general' => 'General HB837 data import'
        ];

        return $descriptions[$type] ?? 'Unknown import type';
    }

    /**
     * Validate import data
     */
    private function validateImportData($data, $mapping)
    {
        $validCount = 0;
        $newCount = 0;
        $updateCount = 0;
        $errors = [];

        // Create field index map
        $fieldIndexMap = [];
        foreach ($mapping as $index => $map) {
            if ($map['target_field'] !== 'unmapped') {
                $fieldIndexMap[$map['target_field']] = $index;
            }
        }

        foreach ($data as $rowIndex => $row) {
            if (empty(array_filter($row)))
                continue; // Skip empty rows

            $isValid = true;
            $hasPropertyName = false;

            // Check for required fields
            if (isset($fieldIndexMap['property_name']) && !empty($row[$fieldIndexMap['property_name']])) {
                $hasPropertyName = true;

                // Check if this property already exists
                $propertyName = trim($row[$fieldIndexMap['property_name']]);
                $exists = HB837::where('property_name', 'like', "%{$propertyName}%")->exists();

                if ($exists) {
                    $updateCount++;
                } else {
                    $newCount++;
                }
            }

            if ($hasPropertyName && $isValid) {
                $validCount++;
            }
        }

        return [
            'valid_count' => $validCount,
            'new_count' => $newCount,
            'update_count' => $updateCount,
            'errors' => $errors
        ];
    }

    /**
     * Generate warnings for import
     */
    private function generateWarnings($mapping, $validationResults)
    {
        $warnings = [];

        // Check for low confidence mappings
        foreach ($mapping as $map) {
            if ($map['confidence'] < 0.5 && $map['target_field'] !== 'unmapped') {
                $warnings[] = "Low confidence mapping for column '{$map['source_column']}' to '{$map['target_field']}'";
            }
        }

        // Check for unmapped columns
        $unmapped = array_filter($mapping, function ($map) {
            return $map['target_field'] === 'unmapped';
        });

        if (!empty($unmapped)) {
            $unmappedColumns = array_column($unmapped, 'source_column');
            $warnings[] = "Unmapped columns: " . implode(', ', $unmappedColumns);
        }

        return $warnings;
    }

    /**
     * Generate preview data
     */
    private function generatePreviewData($filePath, $analysis)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = [];

        // Read file
        if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
        } else if (strtolower($extension) === 'csv') {
            $handle = fopen($filePath, 'r');
            while (($row = fgetcsv($handle)) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }

        $headers = array_shift($data); // Remove header row

        return [
            'headers' => $headers,
            'rows' => $data,
            'total_rows' => count($data)
        ];
    }

    /**
     * Execute intelligent import
     */
    private function executeIntelligentImport($filePath, $analysis)
    {
        // Use the enhanced import system
        $enhancedImport = new \App\Imports\EnhancedHB837Import();

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = [];

        // Read file
        if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
        } else if (strtolower($extension) === 'csv') {
            $handle = fopen($filePath, 'r');
            while (($row = fgetcsv($handle)) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }

        if (empty($data)) {
            throw new \Exception('No data found in file');
        }

        $headers = array_shift($data); // Remove header row

        // Log the import start with detailed analysis
        Log::info('Enhanced Intelligent Import Started', [
            'file_path' => $filePath,
            'total_rows' => count($data),
            'headers' => $headers,
            'analysis_mapping' => $analysis['mapping'] ?? 'No analysis provided'
        ]);

        // Process the import using enhanced rules
        $result = $enhancedImport->processImport($filePath, $headers, $data);

        // Log detailed results
        Log::info('Enhanced Import Results', [
            'imported' => $result['imported'],
            'updated' => $result['updated'],
            'skipped' => $result['skipped'],
            'errors' => count($result['errors']),
            'field_changes_count' => count($result['field_changes'])
        ]);

        // Log field changes for monitoring
        if (!empty($result['field_changes'])) {
            Log::info('Field Changes Summary', [
                'changes_by_record' => $result['field_changes']
            ]);
        }

        return $result;
    }

    /**
     * Sanitize value based on field type
     */
    private function sanitizeValue($field, $value)
    {
        switch ($field) {
            case 'scheduled_date_of_inspection':
            case 'report_submitted':
            case 'billing_req_sent':
            case 'agreement_submitted':
                // Handle date fields
                if (is_numeric($value)) {
                    // Excel date serial number
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                    return $date->format('Y-m-d');
                } else {
                    // Try to parse date string
                    try {
                        return Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                }

            case 'quoted_price':
            case 'sub_fees_estimated_expenses':
            case 'project_net_profit':
                // Handle monetary values
                $cleaned = preg_replace('/[^\d.-]/', '', $value);
                return is_numeric($cleaned) ? (float) $cleaned : null;

            case 'units':
                // Handle integer values
                return is_numeric($value) ? (int) $value : null;

            case 'report_status':
                // Map status values
                $statusMap = [
                    'not started' => 'not-started',
                    'in progress' => 'underway',
                    'in review' => 'in-review',
                    'completed' => 'completed'
                ];
                $lower = strtolower(trim($value));
                return $statusMap[$lower] ?? $lower;

            case 'contracting_status':
                // Map contract status values
                $statusMap = [
                    'quote' => 'quoted',
                    'quoted' => 'quoted',
                    'start' => 'started',
                    'started' => 'started',
                    'execute' => 'executed',
                    'executed' => 'executed',
                    'close' => 'closed',
                    'closed' => 'closed'
                ];
                $lower = strtolower(trim($value));
                return $statusMap[$lower] ?? $lower;

            case 'property_type':
                // Normalize property types to lowercase to match database enum constraint
                $propertyTypeMap = [
                    'garden' => 'garden',
                    'midrise' => 'midrise',
                    'mid-rise' => 'midrise',
                    'mid rise' => 'midrise',
                    'highrise' => 'highrise',
                    'high-rise' => 'highrise',
                    'high rise' => 'highrise',
                    'industrial' => 'industrial',
                    'bungalo' => 'bungalo',
                    'bungalow' => 'bungalo' // Handle common misspelling
                ];
                $lower = strtolower(trim($value));
                return $propertyTypeMap[$lower] ?? $lower;

            default:
                return trim($value);
        }
    }

    /**
     * Show import results page
     */
    public function showImportResults(Request $request)
    {
        $results = $request->session()->get('import_results');

        if (!$results) {
            return redirect()->route('admin.hb837.index')
                ->with('error', 'No import results found.');
        }

        // Clear the session data so it's only shown once
        $request->session()->forget('import_results');

        return view('admin.hb837.import-results', compact('results'));
    }
}
