<?php

namespace App\Modules\HB837\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Modules\HB837\Services\HB837Service;
use App\Modules\HB837\Services\UploadService;
use App\Modules\HB837\Services\ImportService;
use App\Modules\HB837\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HB837ModuleController extends Controller
{
    protected $hb837Service;
    protected $uploadService;
    protected $importService;
    protected $exportService;

    public function __construct(
        HB837Service $hb837Service,
        UploadService $uploadService,
        ImportService $importService,
        ExportService $exportService
    ) {
        $this->hb837Service = $hb837Service;
        $this->uploadService = $uploadService;
        $this->importService = $importService;
        $this->exportService = $exportService;
    }

    /**
     * Display the module dashboard
     */
    public function index(Request $request): View
    {
        $statistics = $this->hb837Service->getStatistics();
        $consultants = $this->hb837Service->getAvailableConsultants();

        return view('modules.hb837.index', compact('statistics', 'consultants'));
    }

    /**
     * Show the import interface
     */
    public function showImport(): View
    {
        return view('modules.hb837.import.index');
    }

    /**
     * Phase 1: Handle file upload
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $fileInfo = $this->uploadService->storeUploadedFile($file);

            // Analyze file structure for Phase 2
            $structure = $this->uploadService->analyzeFileStructure($fileInfo['stored_path']);
            $suggestedMappings = $this->uploadService->getSuggestedMappings($structure['headers'] ?? []);

            return response()->json([
                'success' => true,
                'phase' => 'mapping',
                'file_info' => $fileInfo,
                'structure' => $structure,
                'suggested_mappings' => $suggestedMappings,
                'available_fields' => $this->importService->getAvailableFields(),
                'required_fields' => $this->importService->getRequiredFields(),
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 file upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'File upload failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Phase 2: Handle field mapping and preview
     */
    public function mapFields(Request $request): JsonResponse
    {
        $request->validate([
            'file_path' => 'required|string',
            'mappings' => 'required|array',
            'session_id' => 'required|string',
        ]);

        try {
            $filePath = $request->input('file_path');
            $mappings = $request->input('mappings');

            // Validate file still exists
            if (!Storage::exists($filePath)) {
                throw new \Exception('Upload file not found. Please re-upload.');
            }

            // Validate session
            if (!$this->uploadService->validateSession($request->input('session_id'))) {
                throw new \Exception('Invalid upload session. Please re-upload.');
            }

            // Generate preview
            $preview = $this->importService->previewImport($filePath, $mappings, 10);

            // Validate data
            $validation = $this->importService->validateImportData($filePath, $mappings);

            return response()->json([
                'success' => true,
                'phase' => 'validation',
                'preview' => $preview,
                'validation' => $validation,
                'file_path' => $filePath,
                'mappings' => $mappings,
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 field mapping failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Field mapping failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Phase 3: Execute import
     */
    public function executeImport(Request $request): JsonResponse
    {
        $request->validate([
            'file_path' => 'required|string',
            'mappings' => 'required|array',
            'options' => 'array',
        ]);

        try {
            $filePath = $request->input('file_path');
            $mappings = $request->input('mappings');
            $options = $request->input('options', []);

            // Validate file still exists
            if (!Storage::exists($filePath)) {
                throw new \Exception('Upload file not found. Please re-upload.');
            }

            // Execute import
            $results = $this->importService->executeImport($filePath, $mappings, $options);

            // Clean up uploaded file
            $this->uploadService->cleanupFile($filePath);

            return response()->json([
                'success' => $results['success'],
                'results' => $results,
                'phase' => 'complete'
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 import execution failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Import execution failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Export data
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'status', 'contracting_status', 'consultant_id',
                'date_from', 'date_to', 'city', 'state'
            ]);

            $format = $request->input('format', 'xlsx');

            $filePath = $this->exportService->exportToExcel($filters, $format);

            return response()->json([
                'success' => true,
                'file_path' => $filePath,
                'download_url' => route('modules.hb837.download', ['file' => basename($filePath)]),
                'statistics' => $this->exportService->getExportStatistics(),
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 export failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Export failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download export file
     */
    public function download(Request $request, string $file)
    {
        try {
            $filePath = 'exports/hb837/' . date('Y/m/d') . '/' . $file;

            if (!Storage::exists($filePath)) {
                abort(404, 'File not found');
            }

            return Storage::download($filePath);

        } catch (\Exception $e) {
            Log::error('HB837 download failed', [
                'file' => $file,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            abort(500, 'Download failed');
        }
    }

    /**
     * Get export template
     */
    public function getTemplate(): JsonResponse
    {
        try {
            $filePath = $this->exportService->exportTemplate();

            return response()->json([
                'success' => true,
                'file_path' => $filePath,
                'download_url' => route('modules.hb837.download-template', ['file' => basename($filePath)]),
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 template generation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Template generation failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download template file
     */
    public function downloadTemplate(Request $request, string $file)
    {
        try {
            $filePath = 'templates/hb837/' . $file;

            if (!Storage::exists($filePath)) {
                abort(404, 'Template not found');
            }

            return Storage::download($filePath, 'hb837_import_template.xlsx');

        } catch (\Exception $e) {
            Log::error('HB837 template download failed', [
                'file' => $file,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            abort(500, 'Template download failed');
        }
    }

    /**
     * Create backup
     */
    public function createBackup(): JsonResponse
    {
        try {
            $filePath = $this->exportService->exportBackup();

            return response()->json([
                'success' => true,
                'file_path' => $filePath,
                'download_url' => route('modules.hb837.download-backup', ['file' => basename($filePath)]),
                'statistics' => $this->exportService->getExportStatistics(),
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 backup failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Backup failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup(Request $request, string $file)
    {
        try {
            $filePath = 'backups/hb837/' . date('Y/m/d') . '/' . $file;

            if (!Storage::exists($filePath)) {
                abort(404, 'Backup file not found');
            }

            return Storage::download($filePath);

        } catch (\Exception $e) {
            Log::error('HB837 backup download failed', [
                'file' => $file,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            abort(500, 'Backup download failed');
        }
    }

    /**
     * Get import/export statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->hb837Service->getStatistics();
            $exportStats = $this->exportService->getExportStatistics();

            return response()->json([
                'success' => true,
                'statistics' => array_merge($statistics, $exportStats)
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 statistics failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to get statistics: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Rollback import
     */
    public function rollbackImport(Request $request): JsonResponse
    {
        $request->validate([
            'import_id' => 'required|string',
        ]);

        try {
            $importId = $request->input('import_id');
            $results = $this->importService->rollbackLastImport($importId);

            return response()->json([
                'success' => $results['success'],
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('HB837 rollback failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Rollback failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get data for DataTable
     */
    public function getData(Request $request): JsonResponse
    {
        $query = HB837::with('consultant')
            ->select(['id', 'property_address', 'report_status', 'consultant_id', 'created_at', 'updated_at']);

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('report_status', $request->status);
        }

        if ($request->has('consultant') && $request->consultant) {
            $query->where('consultant_id', $request->consultant);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // DataTable processing
        $totalData = $query->count();

        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('property_address', 'like', "%{$searchValue}%")
                  ->orWhere('report_status', 'like', "%{$searchValue}%");
            });
        }

        $totalFiltered = $query->count();

        // Order
        if ($request->has('order')) {
            $columns = ['property_address', 'report_status', 'consultant_name', 'created_at', 'updated_at'];
            $orderColumn = $columns[$request->order[0]['column']] ?? 'created_at';
            $orderDir = $request->order[0]['dir'] ?? 'desc';
            $query->orderBy($orderColumn, $orderDir);
        }

        // Pagination
        if ($request->has('start')) {
            $query->skip($request->start);
        }
        if ($request->has('length')) {
            $query->take($request->length);
        }

        $data = $query->get()->map(function ($item) {
            return [
                'property_address' => $item->property_address,
                'report_status' => $item->report_status,
                'consultant_name' => $item->consultant ? $item->consultant->name : 'N/A',
                'created_at' => $item->created_at->format('Y-m-d H:i'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i'),
                'actions' => $this->getActionButtons($item)
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    /**
     * Generate action buttons for data table
     */
    private function getActionButtons($item): string
    {
        $buttons = '';

        // View button
        $buttons .= '<a href="#" class="btn btn-sm btn-info mr-1" title="View Details">
                        <i class="fas fa-eye"></i>
                     </a>';

        // Edit button
        $buttons .= '<a href="#" class="btn btn-sm btn-primary mr-1" title="Edit">
                        <i class="fas fa-edit"></i>
                     </a>';

        // Download report button if completed
        if ($item->report_status === 'completed') {
            $buttons .= '<a href="#" class="btn btn-sm btn-success mr-1" title="Download Report">
                            <i class="fas fa-download"></i>
                         </a>';
        }

        // Delete button
        $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteRecord(' . $item->id . ')" title="Delete">
                        <i class="fas fa-trash"></i>
                     </button>';

        return $buttons;
    }
}
