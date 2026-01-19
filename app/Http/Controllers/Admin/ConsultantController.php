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

    /**
     * Display consultant financial summary report
     */
    public function financialReport(Request $request)
    {
        if ($request->ajax()) {
            // Get ALL consultants (no filters) to ensure everyone shows up
            $consultants = Consultant::query()
                ->select('id', 'first_name', 'last_name', 'dba_company_name')
                ->withCount([
                    'hb837Projects as completed_projects' => function ($query) {
                        $query->where('report_status', 'completed');
                    }
                ])
                ->with(['hb837Projects' => function ($query) {
                    $query->where('report_status', 'completed')
                        ->select('id', 'assigned_consultant_id', 'report_status', 'quoted_price', 'sub_fees_estimated_expenses', 'scheduled_date_of_inspection', 'report_submitted');
                }])
                ->orderBy('last_name')
                ->orderBy('first_name');

            return DataTables::of($consultants)
                ->addColumn('name', function ($consultant) {
                    $firstName = trim($consultant->first_name ?? '');
                    $lastName = trim($consultant->last_name ?? '');
                    $fullName = trim($firstName . ' ' . $lastName);
                    return $fullName ?: 'Unnamed Consultant';
                })
                ->addColumn('company', function ($consultant) {
                    return $consultant->dba_company_name ?: 'N/A';
                })
                ->addColumn('completed_projects', function ($consultant) {
                    return $consultant->completed_projects;
                })
                ->addColumn('gross_revenue', function ($consultant) {
                    $grossRevenue = $consultant->hb837Projects->sum(function ($project) {
                        return floatval($project->quoted_price ?? 0);
                    });
                    return '$' . number_format($grossRevenue, 2);
                })
                ->addColumn('estimated_expenses', function ($consultant) {
                    $expenses = $consultant->hb837Projects->sum(function ($project) {
                        return floatval($project->sub_fees_estimated_expenses ?? 0);
                    });
                    return '$' . number_format($expenses, 2);
                })
                ->addColumn('net_revenue', function ($consultant) {
                    $grossRevenue = $consultant->hb837Projects->sum(function ($project) {
                        return floatval($project->quoted_price ?? 0);
                    });
                    $expenses = $consultant->hb837Projects->sum(function ($project) {
                        return floatval($project->sub_fees_estimated_expenses ?? 0);
                    });
                    $netRevenue = $grossRevenue - $expenses;
                    return '$' . number_format($netRevenue, 2);
                })
                ->addColumn('avg_completion_time', function ($consultant) {
                    $today = now();
                    $earliestValid = now()->subYears(10);
                    
                    $validProjects = $consultant->hb837Projects->filter(function ($project) use ($today, $earliestValid) {
                        if (!$project->scheduled_date_of_inspection || !$project->report_submitted) {
                            return false;
                        }
                        
                        $schedDate = $project->scheduled_date_of_inspection;
                        $reportDate = $project->report_submitted;
                        
                        // Filter out bad dates
                        if ($schedDate->year < 1980 || $reportDate->year < 1980) return false; // Epoch/corrupted
                        if ($schedDate->isFuture() || $reportDate->isFuture()) return false; // Future dates
                        if ($schedDate->lt($earliestValid)) return false; // Too old
                        if ($reportDate->lt($schedDate)) return false; // Backwards
                        
                        $daysDiff = $schedDate->diffInDays($reportDate);
                        if ($daysDiff > 365) return false; // Excessive duration
                        
                        return true;
                    });
                    
                    if ($validProjects->isEmpty()) {
                        return '—';
                    }
                    
                    $totalDays = $validProjects->sum(function ($project) {
                        return $project->scheduled_date_of_inspection->diffInDays($project->report_submitted);
                    });
                    
                    $avgDays = $totalDays / $validProjects->count();
                    return number_format($avgDays, 1);
                })
                ->addColumn('actions', function ($consultant) {
                    return '<a href="' . route('admin.consultants.show', $consultant) . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View Details
                    </a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.consultants.report');
    }

    /**
     * Return aggregate metrics for the consultant financial report (charts + summary cards).
     *
     * Uses the same underlying relationships as the DataTables report, but is NOT paginated.
     */
    public function financialReportMetrics(Request $request)
    {
        // Get ALL consultants to ensure everyone shows up
        $consultants = Consultant::query()
            ->select('id', 'first_name', 'last_name', 'dba_company_name')
            ->withCount([
                'hb837Projects as completed_projects' => function ($query) {
                    $query->where('report_status', 'completed');
                },
            ])
            ->with(['hb837Projects' => function ($query) {
                $query->where('report_status', 'completed')
                    ->select('id', 'assigned_consultant_id', 'quoted_price', 'sub_fees_estimated_expenses', 'scheduled_date_of_inspection', 'report_submitted');
            }])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $rows = $consultants->map(function ($consultant) {
            $completedProjects = (int) ($consultant->completed_projects ?? 0);
            
            $grossRevenue = $consultant->hb837Projects->sum(function ($project) {
                return floatval($project->quoted_price ?? 0);
            });
            
            $estimatedExpenses = $consultant->hb837Projects->sum(function ($project) {
                return floatval($project->sub_fees_estimated_expenses ?? 0);
            });
            
            $netRevenue = $grossRevenue - $estimatedExpenses;
            
            $today = now();
            $earliestValid = now()->subYears(10);
            
            $validProjects = $consultant->hb837Projects->filter(function ($project) use ($today, $earliestValid) {
                if (!$project->scheduled_date_of_inspection || !$project->report_submitted) {
                    return false;
                }
                
                $schedDate = $project->scheduled_date_of_inspection;
                $reportDate = $project->report_submitted;
                
                // Filter out bad dates
                if ($schedDate->year < 1980 || $reportDate->year < 1980) return false;
                if ($schedDate->isFuture() || $reportDate->isFuture()) return false;
                if ($schedDate->lt($earliestValid)) return false;
                if ($reportDate->lt($schedDate)) return false;
                
                $daysDiff = $schedDate->diffInDays($reportDate);
                if ($daysDiff > 365) return false;
                
                return true;
            });
            
            $avgCompletionDays = null;
            if ($validProjects->count() > 0) {
                $totalDays = $validProjects->sum(function ($project) {
                    return $project->scheduled_date_of_inspection->diffInDays($project->report_submitted);
                });
                $avgCompletionDays = $totalDays / $validProjects->count();
            }

            $firstName = trim($consultant->first_name ?? '');
            $lastName = trim($consultant->last_name ?? '');
            $fullName = trim($firstName . ' ' . $lastName);

            return [
                'id' => $consultant->id,
                'name' => $fullName ?: 'Unnamed Consultant',
                'company' => $consultant->dba_company_name ?: 'N/A',
                'completed_projects' => $completedProjects,
                'gross_revenue' => $grossRevenue,
                'estimated_expenses' => $estimatedExpenses,
                'net_revenue' => $netRevenue,
                'avg_completion_days' => $avgCompletionDays,
            ];
        })->values();

        $summary = [
            'total_consultants' => $rows->count(),
            'completed_projects' => (int) $rows->sum('completed_projects'),
            'gross_revenue' => $rows->sum('gross_revenue'),
            'estimated_expenses' => $rows->sum('estimated_expenses'),
            'net_revenue' => $rows->sum('net_revenue'),
        ];

        return response()->json([
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }

    /**
     * Identify HB837 projects with suspicious date ranges for data quality review
     */
    public function dateAnomalies()
    {
        $today = now();
        $earliestReasonableDate = now()->subYears(10); // Projects older than 10 years are suspicious
        
        $suspiciousProjects = HB837::query()
            ->where('report_status', 'completed')
            ->whereNotNull('scheduled_date_of_inspection')
            ->whereNotNull('report_submitted')
            ->with('consultant:id,first_name,last_name')
            ->get()
            ->map(function ($project) use ($today, $earliestReasonableDate) {
                $schedDate = $project->scheduled_date_of_inspection;
                $reportDate = $project->report_submitted;
                
                $daysDiff = $schedDate->diffInDays($reportDate, false);
                $absDays = abs($daysDiff);
                
                // Detect specific issues
                $issues = [];
                
                // Check for 1970 dates (Unix epoch default/conversion error)
                if ($schedDate->year == 1970) {
                    $issues[] = '1970 Epoch (Scheduled)';
                }
                if ($reportDate->year == 1970) {
                    $issues[] = '1970 Epoch (Report)';
                }
                
                // Check for dates before 1980 (likely data corruption)
                if ($schedDate->year < 1980 && $schedDate->year != 1970) {
                    $issues[] = 'Pre-1980 Date (Scheduled)';
                }
                if ($reportDate->year < 1980 && $reportDate->year != 1970) {
                    $issues[] = 'Pre-1980 Date (Report)';
                }
                
                // Check for future dates
                if ($schedDate->isFuture()) {
                    $issues[] = 'Future Date (Scheduled)';
                }
                if ($reportDate->isFuture()) {
                    $issues[] = 'Future Date (Report)';
                }
                
                // Check for very old dates (before reasonable project start)
                if ($schedDate->lt($earliestReasonableDate)) {
                    $issues[] = 'Too Old (>10 years)';
                }
                
                // Check for backwards dates
                if ($daysDiff < 0) {
                    $issues[] = 'Backwards (Report before Inspection)';
                }
                
                // Check for excessive duration
                if ($absDays > 365) {
                    $issues[] = 'Excessive Duration (>1 year)';
                }
                
                $isSuspicious = !empty($issues);
                
                return [
                    'id' => $project->id,
                    'property_name' => $project->property_name,
                    'consultant' => $project->consultant ? $project->consultant->first_name . ' ' . $project->consultant->last_name : 'N/A',
                    'scheduled_date' => $schedDate->format('Y-m-d'),
                    'scheduled_year' => $schedDate->year,
                    'report_submitted' => $reportDate->format('Y-m-d'),
                    'report_year' => $reportDate->year,
                    'days_difference' => $daysDiff,
                    'abs_days' => $absDays,
                    'is_backwards' => $daysDiff < 0,
                    'is_1970' => $schedDate->year == 1970 || $reportDate->year == 1970,
                    'is_future' => $schedDate->isFuture() || $reportDate->isFuture(),
                    'is_too_old' => $schedDate->lt($earliestReasonableDate),
                    'issues' => $issues,
                    'is_suspicious' => $isSuspicious,
                    'severity' => $this->calculateSeverity($issues),
                ];
            })
            ->filter(function ($item) {
                return $item['is_suspicious'];
            })
            ->sortByDesc('severity')
            ->sortByDesc('abs_days')
            ->values();

        return view('admin.consultants.date-anomalies', [
            'anomalies' => $suspiciousProjects,
            'total_suspicious' => $suspiciousProjects->count(),
            'by_issue_type' => $this->groupByIssueType($suspiciousProjects),
        ]);
    }
    
    /**
     * Calculate severity score for sorting (higher = worse)
     */
    private function calculateSeverity($issues)
    {
        $score = 0;
        foreach ($issues as $issue) {
            if (str_contains($issue, '1970 Epoch')) $score += 100;
            if (str_contains($issue, 'Pre-1980')) $score += 90;
            if (str_contains($issue, 'Future Date')) $score += 80;
            if (str_contains($issue, 'Too Old')) $score += 70;
            if (str_contains($issue, 'Backwards')) $score += 60;
            if (str_contains($issue, 'Excessive Duration')) $score += 50;
        }
        return $score;
    }
    
    /**
     * Group anomalies by issue type for summary
     */
    private function groupByIssueType($anomalies)
    {
        $grouped = [
            'epoch_1970' => 0,
            'pre_1980' => 0,
            'future_dates' => 0,
            'too_old' => 0,
            'backwards' => 0,
            'excessive_duration' => 0,
        ];
        
        foreach ($anomalies as $anomaly) {
            foreach ($anomaly['issues'] as $issue) {
                if (str_contains($issue, '1970 Epoch')) $grouped['epoch_1970']++;
                if (str_contains($issue, 'Pre-1980')) $grouped['pre_1980']++;
                if (str_contains($issue, 'Future Date')) $grouped['future_dates']++;
                if (str_contains($issue, 'Too Old')) $grouped['too_old']++;
                if (str_contains($issue, 'Backwards')) $grouped['backwards']++;
                if (str_contains($issue, 'Excessive Duration')) $grouped['excessive_duration']++;
            }
        }
        
        return $grouped;
    }
}
