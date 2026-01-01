<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Models\HB837;
use App\Models\Client;
use App\Models\HB837File;
use App\Models\Consultant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\HB837\HB837DataTableService;
use App\Services\HB837\HB837StatisticsService;
use App\Services\HB837\HB837FileService;
use App\Services\HB837\HB837BulkActionService;
use App\Services\HB837\HB837ReportService;
use App\Services\HB837\HB837ValidationService;

class HB837ControllerRefactored extends Controller
{
    protected $dataTableService;
    protected $statisticsService;
    protected $fileService;
    protected $bulkActionService;
    protected $reportService;
    protected $validationService;

    public function __construct(
        HB837DataTableService $dataTableService,
        HB837StatisticsService $statisticsService,
        HB837FileService $fileService,
        HB837BulkActionService $bulkActionService,
        HB837ReportService $reportService,
        HB837ValidationService $validationService
    ) {
        $this->dataTableService = $dataTableService;
        $this->statisticsService = $statisticsService;
        $this->fileService = $fileService;
        $this->bulkActionService = $bulkActionService;
        $this->reportService = $reportService;
        $this->validationService = $validationService;
    }

    /**
     * Display HB837 index with tabs and DataTables
     */
    public function index(Request $request, $tab = 'active')
    {
        $tab = $this->validationService->validateTab($tab);

        if ($request->ajax()) {
            return $this->dataTableService->getDatatablesData($tab, $request);
        }

        $stats = $this->statisticsService->getGeneralStatistics();
        $tabCounts = $this->statisticsService->getTabCounts();
        $warnings = $this->statisticsService->getWarningMetrics();
        $business = $this->statisticsService->getBusinessMetrics();

        return view('admin.hb837.index', [
            'tab' => $tab,
            'stats' => $stats,
            'tabCounts' => $tabCounts,
            'warnings' => $warnings,
            'business' => $business
        ]);
    }

    /**
     * Show the form for creating a new HB837 record
     */
    public function create()
    {
        $consultants = Consultant::where('is_active', true)->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        
        return view('admin.hb837.create', compact('consultants', 'clients'));
    }

    /**
     * Store a newly created HB837 record
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationService->getCreateRules());
        $validated = $this->validationService->processValidatedData($validated);

        $hb837 = HB837::create($validated);

        return redirect()->route('admin.hb837.edit', $hb837->id)
            ->with('success', 'HB837 record created successfully.');
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
    public function edit(HB837 $hb837)
    {
        $consultants = Consultant::where('is_active', true)->orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $hb837->load(['consultant', 'user', 'files']);
        
        return view('admin.hb837.edit', compact('hb837', 'consultants', 'clients'));
    }

    /**
     * Update the specified HB837 record
     */
    public function update(Request $request, HB837 $hb837)
    {
        $validated = $request->validate($this->validationService->getUpdateRules());
        $validated = $this->validationService->processValidatedData($validated);

        $hb837->update($validated);

        return redirect()->route('admin.hb837.edit', $hb837->id)
            ->with('success', 'HB837 record updated successfully.');
    }

    /**
     * Remove the specified HB837 record
     */
    public function destroy(HB837 $hb837)
    {
        $this->fileService->deleteAllFiles($hb837);
        $hb837->delete();

        return redirect()->route('admin.hb837.index')
            ->with('success', 'HB837 record deleted successfully.');
    }

    /**
     * Update status of HB837 record via AJAX
     */
    public function updateStatus(Request $request, HB837 $hb837)
    {
        $this->validationService->validateStatusUpdate($request);
        
        return $this->bulkActionService->updateSingleRecord($hb837, ['report_status' => $request->status]);
    }

    /**
     * Update priority of HB837 record via AJAX
     */
    public function updatePriority(Request $request, HB837 $hb837)
    {
        $this->validationService->validatePriorityUpdate($request);
        
        return $this->bulkActionService->updateSingleRecord($hb837, ['priority' => $request->priority]);
    }

    /**
     * Handle bulk actions on selected HB837 records
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,update_status,update_priority,export',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:hb837,id',
            'status' => 'required_if:action,update_status|in:not-started,underway,in-review,completed',
            'priority' => 'required_if:action,update_priority|in:low,normal,high,urgent'
        ]);

        return $this->bulkActionService->handleBulkAction($request);
    }

    /**
     * Export HB837 data
     */
    public function export(Request $request)
    {
        $this->validationService->validateExportRequest($request);
        
        return $this->reportService->export($request);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.hb837.import');
    }

    /**
     * Handle import process
     */
    public function import(Request $request)
    {
        $this->validationService->validateImportRequest($request);
        
        return $this->fileService->handleImport($request);
    }

    /**
     * Search HB837 records
     */
    public function search(Request $request)
    {
        $this->validationService->validateSearchRequest($request);
        
        return $this->dataTableService->searchRecords($request->q);
    }

    /**
     * Generate PDF report
     */
    public function generateReport(Request $request, HB837 $hb837 = null)
    {
        if ($hb837) {
            return $this->reportService->generatePdfReport($hb837);
        } else {
            return $this->reportService->generateBulkPdfReport($request->all());
        }
    }

    /**
     * Get Google Maps embed for property address
     */
    public function getMapsEmbed(HB837 $hb837)
    {
        return $this->reportService->getMapsEmbedUrl($hb837);
    }

    // File Management Methods
    
    /**
     * Upload file for HB837 record
     */
    public function uploadFile(Request $request, HB837 $hb837)
    {
        return $this->fileService->uploadFile($request, $hb837);
    }

    /**
     * Download file
     */
    public function downloadFile(HB837File $file)
    {
        return $this->fileService->downloadFile($file);
    }

    /**
     * Delete file
     */
    public function deleteFile(HB837File $file)
    {
        return $this->fileService->deleteFile($file);
    }

    /**
     * List files for HB837 record
     */
    public function listFiles(HB837 $hb837)
    {
        return $this->fileService->listFiles($hb837);
    }

    // Statistics and Dashboard Methods

    /**
     * Get dashboard statistics via AJAX
     */
    public function getDashboardStats()
    {
        return response()->json([
            'stats' => $this->statisticsService->getGeneralStatistics(),
            'warnings' => $this->statisticsService->getWarningMetrics(),
            'business' => $this->statisticsService->getBusinessMetrics()
        ]);
    }

    /**
     * Get consultant performance statistics
     */
    public function getConsultantStats()
    {
        return response()->json($this->statisticsService->getConsultantStats());
    }

    /**
     * Get monthly statistics
     */
    public function getMonthlyStats(Request $request)
    {
        $year = $request->get('year', date('Y'));
        return response()->json($this->statisticsService->getMonthlyStats($year));
    }

    // API Methods for DataTables

    /**
     * Get filtered data for specific tab via AJAX
     */
    public function getTabData(Request $request, $tab)
    {
        $tab = $this->validationService->validateTab($tab);
        return $this->dataTableService->getDatatablesData($tab, $request);
    }

    /**
     * Get column visibility settings
     */
    public function getColumnSettings()
    {
        return response()->json($this->dataTableService->getColumnSettings());
    }

    /**
     * Save column visibility settings
     */
    public function saveColumnSettings(Request $request)
    {
        return $this->dataTableService->saveColumnSettings($request);
    }
}
