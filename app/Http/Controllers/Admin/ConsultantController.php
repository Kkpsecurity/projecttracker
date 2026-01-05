<?php

namespace App\Http\Controllers\Admin;

use App\Models\Consultant;
use App\Models\ConsultantFile;
use App\Models\HB837;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class ConsultantController extends Controller
{
    /**
     * Display a listing of consultants
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $consultants = Consultant::query()
                ->withCount(['hb837Projects as active_assignments' => function ($query) {
                    $query->whereNotIn('report_status', ['completed']);
                }])
                ->withCount(['hb837Projects as completed_assignments' => function ($query) {
                    $query->where('report_status', 'completed');
                }])
                ->withCount('files');

            return DataTables::of($consultants)
                ->addColumn('checkbox', function ($consultant) {
                    return '<input type="checkbox" name="consultant_ids[]" value="' . $consultant->id . '" class="consultant-checkbox">';
                })
                ->addColumn('name', function ($consultant) {
                    return $consultant->first_name . ' ' . $consultant->last_name;
                })
                ->addColumn('company', function ($consultant) {
                    return $consultant->dba_company_name ?: 'N/A';
                })
                ->addColumn('fcp_status', function ($consultant) {
                    if (!$consultant->fcp_expiration_date) {
                        return '<span class="badge badge-secondary">No Date</span>';
                    }

                    $daysUntilExpiry = now()->diffInDays($consultant->fcp_expiration_date, false);

                    if ($daysUntilExpiry < 0) {
                        return '<span class="badge badge-danger">Expired</span>';
                    } elseif ($daysUntilExpiry <= 30) {
                        return '<span class="badge badge-warning">Expires Soon</span>';
                    } else {
                        return '<span class="badge badge-success">Valid</span>';
                    }
                })
                ->addColumn('assignments', function ($consultant) {
                    return sprintf(
                        '<span class="badge badge-primary">%d Active</span> <span class="badge badge-success">%d Completed</span>',
                        $consultant->active_assignments,
                        $consultant->completed_assignments
                    );
                })
                ->addColumn('action', function ($consultant) {
                    return '
                        <div class="btn-group" role="group">
                            <a href="' . route('admin.consultants.show', $consultant->id) . '" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="' . route('admin.consultants.edit', $consultant->id) . '" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-consultant" data-id="' . $consultant->id . '" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['checkbox', 'fcp_status', 'assignments', 'action'])
                ->make(true);
        }

        return view('admin.consultants.index');
    }

    /**
     * Show the form for creating a new consultant
     */
    public function create()
    {
        return view('admin.consultants.create');
    }

    /**
     * Store a newly created consultant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants,email',
            'dba_company_name' => 'nullable|string|max:255',
            'mailing_address' => 'nullable|string',
            'fcp_expiration_date' => 'nullable|date',
            'assigned_light_meter' => 'nullable|string|max:255',
            'lm_nist_expiration_date' => 'nullable|date',
            'subcontractor_bonus_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $consultant = Consultant::create($validated);

        return redirect()->route('admin.consultants.show', $consultant->id)
            ->with('success', 'Consultant created successfully!');
    }

    /**
     * Display the specified consultant with tabs
     */
    public function show(Consultant $consultant, $tab = 'information')
    {
        $tab = in_array($tab, ['information', 'active-assignments', 'completed-assignments', 'financials', 'files']) ? $tab : 'information';

        $consultant->load('files');

        // Get assignments data for tabs
        $activeAssignments = HB837::where('assigned_consultant_id', $consultant->id)
            ->whereNotIn('report_status', ['completed'])
            ->with(['consultant'])
            ->get();

        $completedAssignments = HB837::where('assigned_consultant_id', $consultant->id)
            ->where('report_status', 'completed')
            ->with(['consultant'])
            ->get();

        $allAssignments = $activeAssignments->concat($completedAssignments);

        $sumMoney = function ($items, string $field): float {
            return (float) $items->sum(function ($x) use ($field) {
                $value = data_get($x, $field);
                return $value === null ? 0.0 : (float) $value;
            });
        };

        $activeGross = $sumMoney($activeAssignments, 'quoted_price');
        $activeExpenses = $sumMoney($activeAssignments, 'sub_fees_estimated_expenses');
        $activeNet = $activeGross - $activeExpenses;

        $completedGross = $sumMoney($completedAssignments, 'quoted_price');
        $completedExpenses = $sumMoney($completedAssignments, 'sub_fees_estimated_expenses');
        $completedNet = $completedGross - $completedExpenses;

        $allGross = $activeGross + $completedGross;
        $allExpenses = $activeExpenses + $completedExpenses;
        $allNet = $allGross - $allExpenses;

        $completionDaysValues = $completedAssignments
            ->filter(fn($hb) => $hb->scheduled_date_of_inspection && $hb->report_submitted)
            ->map(fn($hb) => (int) $hb->scheduled_date_of_inspection->diffInDays($hb->report_submitted));

        $completedAvgCompletionDays = $completionDaysValues->count() ? (float) $completionDaysValues->avg() : null;

        $financialSummary = [
            'active' => [
                'count' => $activeAssignments->count(),
                'gross' => $activeGross,
                'expenses' => $activeExpenses,
                'net' => $activeNet,
            ],
            'completed' => [
                'count' => $completedAssignments->count(),
                'gross' => $completedGross,
                'expenses' => $completedExpenses,
                'net' => $completedNet,
                'avg_completion_days' => $completedAvgCompletionDays,
                'avg_completion_days_count' => $completionDaysValues->count(),
            ],
            'all' => [
                'count' => $allAssignments->count(),
                'gross' => $allGross,
                'expenses' => $allExpenses,
                'net' => $allNet,
            ],
        ];

        return view('admin.consultants.show', compact(
            'consultant',
            'tab',
            'activeAssignments',
            'completedAssignments',
            'allAssignments',
            'financialSummary'
        ));
    }

    /**
     * Per-consultant Activity Report PDF.
     *
     * This is a simple baseline PDF; more data can be added later.
     */
    public function activityReportPdf(Consultant $consultant)
    {
        $consultant->load('files');

        $baseQuery = HB837::query()
            ->where('assigned_consultant_id', $consultant->id)
            ->withCount([
                'files',
                'plots',
                'findings',
                'riskMeasures',
                'recentIncidents',
            ])
            ->with([
                'crimeStats',
                'crimeStats.reviewer',
                'recentIncidents' => function ($q) {
                    $q->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->orderByDesc('updated_at');

        $activeAssignments = (clone $baseQuery)
            ->whereNotIn('report_status', ['completed'])
            ->get();

        $completedAssignments = (clone $baseQuery)
            ->where('report_status', 'completed')
            ->get();

        $generatedAt = now();
        $generatedBy = Auth::user()->name ?? 'System';

        $pdf = Pdf::loadView('admin.consultants.pdf-activity-report', compact(
            'consultant',
            'generatedAt',
            'generatedBy',
            'activeAssignments',
            'completedAssignments'
        ))->setPaper('letter', 'portrait');

        $filename = sprintf(
            'consultant_activity_report_%s_%s.pdf',
            $consultant->id,
            $generatedAt->format('Ymd_His')
        );

        return $pdf->stream($filename);
    }

    /**
     * Show the form for editing the specified consultant
     */
    public function edit(Consultant $consultant)
    {
        return view('admin.consultants.edit', compact('consultant'));
    }

    /**
     * Update the specified consultant
     */
    public function update(Request $request, Consultant $consultant)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants,email,' . $consultant->id,
            'dba_company_name' => 'nullable|string|max:255',
            'mailing_address' => 'nullable|string',
            'fcp_expiration_date' => 'nullable|date',
            'assigned_light_meter' => 'nullable|string|max:255',
            'lm_nist_expiration_date' => 'nullable|date',
            'subcontractor_bonus_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $consultant->update($validated);

        return redirect()->route('admin.consultants.show', $consultant->id)
            ->with('success', 'Consultant updated successfully!');
    }

    /**
     * Remove the specified consultant
     */
    public function destroy(Consultant $consultant)
    {
        // Check if consultant has active assignments
        $activeAssignments = HB837::where('assigned_consultant_id', $consultant->id)->count();

        if ($activeAssignments > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete consultant with active assignments.');
        }

        $consultant->delete();

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant deleted successfully!');
    }

    /**
     * Upload file for consultant
     */
    public function uploadFile(Request $request, Consultant $consultant)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'description' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $storedName = time() . '_' . $originalName;
        $path = $file->storeAs('consultant_files', $storedName, 'public');

        ConsultantFile::create([
            'consultant_id' => $consultant->id,
            'original_filename' => $originalName,
            'stored_filename' => $storedName,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
        ]);

        return redirect()->back()
            ->with('success', 'File uploaded successfully: ' . $originalName);
    }

    /**
     * Download consultant file
     */
    public function downloadFile(ConsultantFile $file)
    {
        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found');
        }

        return Response::download(
            Storage::disk('public')->path($file->file_path),
            $file->original_filename
        );
    }

    /**
     * Delete consultant file
     */
    public function deleteFile(ConsultantFile $file)
    {
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->back()
            ->with('success', 'File deleted successfully');
    }
}
