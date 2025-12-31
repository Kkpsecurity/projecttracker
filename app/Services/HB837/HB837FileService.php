<?php

namespace App\Services\HB837;

use App\Jobs\HB837\ExtractHB837CrimeStatsJob;
use App\Models\HB837;
use App\Models\HB837File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class HB837FileService
{
    /**
     * Upload file for a specific HB837 record
     */
    public function uploadFile(Request $request, HB837 $hb837): array
    {
        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('hb837/' . $hb837->id, $filename, 'public');

            $fileCategory = $this->normalizeFileCategory($request->input('file_category'));

            $hb837File = HB837File::create([
                'hb837_id' => $hb837->id,
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'file_category' => $fileCategory,
                'description' => $request->description,
                'uploaded_by' => Auth::id()
            ]);

            // Phase 3: automatically extract crime stats when a crime report is uploaded.
            if ($fileCategory === 'crime_report' && ($hb837File->mime_type ?? '') === 'application/pdf') {
                ExtractHB837CrimeStatsJob::dispatch($hb837File->id);
            }

            return [
                'success' => true,
                'message' => 'File uploaded successfully!',
                'file' => $hb837File
            ];

        } catch (\Exception $e) {
            Log::error('HB837 File Upload Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Download a specific file
     */
    public function downloadFile(HB837File $file): Response
    {
        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        $fullPath = Storage::disk('public')->path($file->file_path);
        return Response::download($fullPath, $file->original_filename);
    }

    /**
     * Delete a specific file
     */
    public function deleteFile(HB837File $file): array
    {
        try {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            $file->delete();

            return [
                'success' => true,
                'message' => 'File deleted successfully.'
            ];

        } catch (\Exception $e) {
            Log::error('HB837 File Delete Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'File deletion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate file upload request
     */
    public function validateUpload(Request $request): void
    {
        $allowedCategories = (array) Config::get('hb837.file_categories', []);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'file_category' => empty($allowedCategories)
                ? 'nullable|string|max:50'
                : 'nullable|string|in:' . implode(',', $allowedCategories),
            'description' => 'nullable|string|max:255'
        ]);
    }

    private function normalizeFileCategory(mixed $value): string
    {
        $category = trim((string) ($value ?? ''));

        if ($category === '') {
            return 'other';
        }

        return $category;
    }

    /**
     * Get file size in human readable format
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Check if file type is allowed
     */
    public function isFileTypeAllowed(string $mimeType): bool
    {
        $allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png',
            'image/gif',
            'text/plain',
            'text/csv'
        ];

        return in_array($mimeType, $allowedTypes);
    }

    /**
     * Get file icon based on mime type
     */
    public function getFileIcon(string $mimeType): string
    {
        $iconMap = [
            'application/pdf' => 'fas fa-file-pdf text-danger',
            'application/msword' => 'fas fa-file-word text-primary',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word text-primary',
            'application/vnd.ms-excel' => 'fas fa-file-excel text-success',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel text-success',
            'image/jpeg' => 'fas fa-file-image text-info',
            'image/png' => 'fas fa-file-image text-info',
            'image/gif' => 'fas fa-file-image text-info',
            'text/plain' => 'fas fa-file-alt text-secondary',
            'text/csv' => 'fas fa-file-csv text-warning'
        ];

        return $iconMap[$mimeType] ?? 'fas fa-file text-muted';
    }

    /**
     * Delete all files for a given HB837 record
     */
    public function deleteAllFiles(HB837 $hb837): bool
    {
        try {
            $files = $hb837->files;
            
            foreach ($files as $file) {
                $this->deleteFile($file);
            }
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting all files for HB837 ' . $hb837->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * List files for HB837 record
     */
    public function listFiles(HB837 $hb837): \Illuminate\Http\JsonResponse
    {
        try {
            $files = $hb837->files->map(function ($file) {
                return [
                    'id' => $file->id,
                    'original_name' => $file->original_name,
                    'file_size' => $this->formatFileSize($file->file_size),
                    'mime_type' => $file->mime_type,
                    'icon' => $this->getFileIcon($file->mime_type),
                    'uploaded_at' => $file->created_at->format('M j, Y g:i A'),
                    'download_url' => route('admin.hb837.download-file', $file->id)
                ];
            });

            return response()->json(['files' => $files]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to list files'], 500);
        }
    }

    /**
     * Handle import process
     */
    public function handleImport(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $file = $request->file('file');
            $action = $request->input('action', 'preview');
            
            if ($action === 'preview') {
                // Store file temporarily and show preview
                $path = $file->storeAs('temp', 'import_preview.xlsx');
                
                // Process first few rows for preview
                $preview = $this->getImportPreview($path);
                
                return redirect()->back()->with([
                    'import_preview' => $preview,
                    'temp_file_path' => $path
                ]);
            } else {
                // Perform actual import
                $import = new \App\Imports\HB837Import();
                \Maatwebsite\Excel\Facades\Excel::import($import, $file);
                
                return redirect()->route('admin.hb837.index')
                    ->with('success', 'Import completed successfully');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get import preview data
     */
    private function getImportPreview(string $path): array
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/' . $path));
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            return [
                'headers' => array_shift($data),
                'rows' => array_slice($data, 0, 5), // First 5 rows
                'total_rows' => count($data)
            ];
        } catch (\Exception $e) {
            return ['error' => 'Failed to read file'];
        }
    }
}
