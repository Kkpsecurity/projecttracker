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

class HB837Controller extends Controller
{
    /**
     * Display HB837 index with tabs and DataTables
     */
    public function index(Request $request, $tab = 'active')
    {
        $tab = in_array($tab = Str::lower($tab), ['active', 'quoted', 'completed', 'closed']) ? $tab : 'active';

        if ($request->ajax()) {
            return $this->getDatatablesData($tab);
        }

        // Calculate statistics for dashboard cards
        $stats = [
            'active' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
                ->where('contracting_status', 'executed')->count(),
            'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
            'total' => HB837::count()
        ];

        return view('admin.hb837.index', [
            'tab' => $tab,
            'stats' => $stats
        ]);
    }

    /**
     * Get DataTables data with color coding for Issue #8
     */
    private function getDatatablesData($tab)
    {
        $query = HB837::query()->with(['consultant', 'user']);

        // Apply tab filters
        $this->applyTabFilters($query, $tab);

        return DataTables::of($query)
            ->addColumn('checkbox', function ($hb837) {
                return '<input type="checkbox" class="bulk-checkbox" value="' . $hb837->id . '">';
            })
            ->addColumn('action', function ($hb837) {
                return '
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="' . route('admin.hb837.show', $hb837->id) . '"
                           class="btn btn-info" title="View Details" data-toggle="tooltip">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('admin.hb837.edit', $hb837->id) . '"
                           class="btn btn-primary" title="Edit Record" data-toggle="tooltip">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="duplicateRecord(' . $hb837->id . ')"
                                class="btn btn-secondary" title="Duplicate Record" data-toggle="tooltip">
                            <i class="fas fa-copy"></i>
                        </button>
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
            ->editColumn('securitygauge_crime_risk', function ($hb837) {
                return $this->getCrimeRiskCell($hb837->securitygauge_crime_risk);
            })
            ->editColumn('report_status', function ($hb837) {
                return $this->getReportStatusCell($hb837->report_status);
            })
            ->editColumn('contracting_status', function ($hb837) {
                return $this->getContractingStatusCell($hb837->contracting_status);
            })
            ->editColumn('assigned_consultant_id', function ($hb837) {
                return $hb837->consultant ?
                    $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name :
                    '<span class="text-muted">Unassigned</span>';
            })
            ->editColumn('scheduled_date_of_inspection', function ($hb837) {
                if ($hb837->scheduled_date_of_inspection) {
                    $date = \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection);
                    $isOverdue = $date->isPast() && $hb837->report_status !== 'completed';
                    $class = $isOverdue ? 'text-danger font-weight-bold' : '';
                    return '<span class="' . $class . '">' . $date->format('M j, Y') . '</span>';
                }
                return '<span class="text-muted">Not scheduled</span>';
            })
            ->editColumn('quoted_price', function ($hb837) {
                if ($hb837->quoted_price) {
                    return '$' . number_format($hb837->quoted_price, 2);
                }
                return '<span class="text-muted">Not quoted</span>';
            })
            ->editColumn('priority', function ($hb837) {
                $score = $this->calculatePriorityScore($hb837);
                $labels = [
                    0 => '<span class="badge badge-secondary">Low</span>',
                    1 => '<span class="badge badge-primary">Normal</span>',
                    2 => '<span class="badge badge-warning">High</span>',
                    3 => '<span class="badge badge-danger">Urgent</span>'
                ];
                return $labels[$score] ?? $labels[0];
            })
            ->editColumn('created_at', function ($hb837) {
                return $hb837->created_at->format('M j, Y');
            })
            ->editColumn('county', function ($hb837) {
                return $hb837->county ?: '<span class="text-muted">Not specified</span>';
            })
            ->editColumn('macro_client', function ($hb837) {
                return $hb837->macro_client ?: '<span class="text-muted">Not assigned</span>';
            })
            ->rawColumns(['checkbox', 'action', 'property_name', 'report_status', 'contracting_status', 'assigned_consultant_id', 'scheduled_date_of_inspection', 'quoted_price', 'priority', 'securitygauge_crime_risk', 'county', 'macro_client'])
            ->make(true);
    }

    /**
     * Get color-coded crime risk cell per GitHub Issue #8
     */
    private function getCrimeRiskCell($risk)
    {
        if (!$risk) return '<span class="text-muted">Not assessed</span>';

        $colors = [
            'Low' => ['bg' => '#72b862', 'text' => 'white'],
            'Moderate' => ['bg' => '#95f181', 'text' => 'black'],
            'Elevated' => ['bg' => '#fae099', 'text' => 'black'],
            'High' => ['bg' => '#f2a36e', 'text' => 'black'],
            'Severe' => ['bg' => '#c75845', 'text' => 'white']
        ];

        $style = isset($colors[$risk]) ?
            'background-color: ' . $colors[$risk]['bg'] . '; color: ' . $colors[$risk]['text'] . ';' : '';

        return '<span class="badge px-3 py-2" style="' . $style . '">' . e($risk) . '</span>';
    }

    /**
     * Get color-coded report status cell per GitHub Issue #8
     */
    private function getReportStatusCell($status)
    {
        if (!$status) return '<span class="text-muted">No status</span>';

        $colors = [
            'not-started' => ['bg' => '#f8d7da', 'text' => '#721c24'],
            'in-progress' => ['bg' => '#fff3cd', 'text' => '#856404'],
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
            case 'active':
                $query->whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
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
            'report_status' => 'nullable|in:not-started,in-progress,in-review,completed',
            'contracting_status' => 'nullable|in:quoted,started,executed,closed',
            'quoted_price' => 'nullable|numeric',
            'sub_fees_estimated_expenses' => 'nullable|numeric',
            'billing_req_sent' => 'nullable|date',
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
            'notes' => 'nullable|string'
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

        return redirect()->route('admin.hb837.edit', $hb837->id)
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
            'report_status' => 'nullable|in:not-started,in-progress,in-review,completed',
            'contracting_status' => 'nullable|in:quoted,started,executed,closed',
            'quoted_price' => 'nullable|numeric',
            'sub_fees_estimated_expenses' => 'nullable|numeric',
            'billing_req_sent' => 'nullable|date',
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
            'notes' => 'nullable|string'
        ]);

        // Calculate net profit
        if (isset($validated['quoted_price']) && isset($validated['sub_fees_estimated_expenses'])) {
            $validated['project_net_profit'] = $validated['quoted_price'] - $validated['sub_fees_estimated_expenses'];
        }

        $hb837->update($validated);

        return redirect()->route('admin.hb837.edit', ['hb837' => $hb837->id, 'tab' => $tabId])
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
     * TODO: Implement HB837Export class
     */
    public function export(Request $request)
    {
        $filename = 'hb837_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        // TODO: Create HB837Export class
        // return Excel::download(new HB837Export($request->get('tab', 'active')), $filename);

        return redirect()->back()->with('info', 'Export functionality will be implemented with HB837Export class.');
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
        if ($request->ajax()) {
            return $this->getDatatablesData($tab);
        }

        return response()->json(['error' => 'Invalid request'], 400);
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
            'bulk_status' => 'nullable|in:not-started,in-progress,in-review,completed',
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
            'status' => 'required|in:not-started,in-progress,in-review,completed'
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

        // TODO: Create HB837Export class with format support
        // return Excel::download(new HB837Export($request->get('tab', 'active'), $format), $filename);

        return redirect()->back()->with('info',
            "Export functionality will be implemented with HB837Export class for {$format} format."
        );
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
                'filename' => $file->getClientOriginalName(),
                'stored_filename' => $filename,
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
        return Response::download($fullPath, $file->filename);
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

            return redirect()->route('admin.hb837.edit', $duplicate->id)
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
     * Get updated statistics for dashboard cards (AJAX endpoint)
     */
    public function getStats()
    {
        $stats = [
            'active' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
                ->where('contracting_status', 'executed')->count(),
            'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
            'total' => HB837::count()
        ];

        return response()->json($stats);
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
            $extension = $file->getClientOriginalExtension();

            // Store file temporarily
            $fileName = 'import_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath = $file->storeAs('temp/imports', $fileName);

            // Analyze file structure
            $analysis = $this->performFileAnalysis(storage_path('app/' . $filePath));

            // Store analysis data temporarily
            $fileId = uniqid();
            Cache::put("import_analysis_{$fileId}", [
                'file_path' => $filePath,
                'original_name' => $originalName,
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

            return response()->json([
                'success' => true,
                'imported' => $result['imported'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors']
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
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = [];
        $headers = [];

        // Read file based on type
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

        // Extract headers (assume first row)
        $headers = array_shift($data);
        $headers = array_map('trim', $headers);

        // Analyze file content
        $stats = [
            'total_rows' => count($data),
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
     * Intelligent column mapping using fuzzy matching
     */
    private function intelligentColumnMapping($headers)
    {
        $fieldMappings = [
            'property_name' => ['property name', 'property', 'name', 'building name', 'complex name'],
            'address' => ['address', 'street', 'location', 'street address'],
            'city' => ['city', 'town'],
            'county' => ['county', 'parish'],
            'state' => ['state', 'province', 'st'],
            'zip' => ['zip', 'zipcode', 'postal', 'postal code'],
            'phone' => ['phone', 'telephone', 'tel', 'contact'],
            'management_company' => ['management', 'company', 'mgmt', 'manager'],
            'owner_name' => ['owner', 'property owner', 'landlord'],
            'property_type' => ['type', 'property type', 'building type'],
            'units' => ['units', 'unit count', 'number of units'],
            'securitygauge_crime_risk' => ['crime risk', 'risk', 'security risk', 'crime', 'risk level'],
            'macro_client' => ['macro client', 'client', 'parent company'],
            'property_manager_name' => ['property manager', 'pm', 'manager name'],
            'property_manager_email' => ['pm email', 'manager email', 'property manager email'],
            'regional_manager_name' => ['regional manager', 'rm', 'regional'],
            'regional_manager_email' => ['rm email', 'regional email', 'regional manager email'],
            'report_status' => ['status', 'report status', 'progress'],
            'contracting_status' => ['contract status', 'contract', 'phase'],
            'scheduled_date_of_inspection' => ['inspection date', 'scheduled', 'date'],
            'quoted_price' => ['price', 'quote', 'quoted price', 'amount']
        ];

        $mappings = [];

        foreach ($headers as $header) {
            $bestMatch = null;
            $bestScore = 0;

            foreach ($fieldMappings as $field => $patterns) {
                foreach ($patterns as $pattern) {
                    $score = $this->calculateSimilarity(strtolower(trim($header)), strtolower($pattern));
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMatch = $field;
                    }
                }
            }

            $mappings[] = [
                'source_column' => $header,
                'target_field' => $bestMatch ?: 'unmapped',
                'confidence' => $bestScore
            ];
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

        $levenshtein = levenshtein($str1, $str2);
        $maxLen = max($len1, $len2);

        $similarity = 1 - ($levenshtein / $maxLen);

        // Boost score for exact substring matches
        if (strpos($str1, $str2) !== false || strpos($str2, $str1) !== false) {
            $similarity += 0.3;
        }

        return min(1, $similarity);
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

        // Create field index map
        $fieldIndexMap = [];
        foreach ($analysis['mapping'] as $index => $map) {
            if ($map['target_field'] !== 'unmapped' && $map['confidence'] > 0.5) {
                $fieldIndexMap[$map['target_field']] = $index;
            }
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($data as $rowIndex => $row) {
            if (empty(array_filter($row))) {
                $skipped++;
                continue;
            }

            try {
                $recordData = [];

                // Map data according to analysis
                foreach ($fieldIndexMap as $field => $index) {
                    if (isset($row[$index])) {
                        $value = trim($row[$index]);
                        if ($value !== '') {
                            $recordData[$field] = $this->sanitizeValue($field, $value);
                        }
                    }
                }

                if (empty($recordData['property_name'])) {
                    $skipped++;
                    continue;
                }

                // Check if record exists
                $existing = HB837::where('property_name', 'like', "%{$recordData['property_name']}%")->first();

                if ($existing) {
                    // Update existing record
                    $existing->update($recordData);
                    $updated++;
                } else {
                    // Create new record
                    HB837::create($recordData);
                    $imported++;
                }

            } catch (\Exception $e) {
                $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                $skipped++;
            }
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors
        ];
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
                    'in progress' => 'in-progress',
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

            default:
                return trim($value);
        }
    }
}
