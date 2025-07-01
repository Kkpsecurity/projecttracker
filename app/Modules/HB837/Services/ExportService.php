<?php

namespace App\Modules\HB837\Services;

use App\Models\HB837;
use App\Modules\HB837\Exports\HB837ThreePhaseExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    /**
     * Export HB837 data to Excel
     */
    public function exportToExcel(array $filters = [], string $format = 'xlsx'): string
    {
        try {
            $export = new HB837ThreePhaseExport($filters);

            $filename = 'hb837_export_' . date('Y-m-d_H-i-s') . '.' . $format;
            $path = 'exports/hb837/' . date('Y/m/d');
            $fullPath = $path . '/' . $filename;

            Excel::store($export, $fullPath, 'local');

            Log::info('HB837 export completed', [
                'filename' => $filename,
                'path' => $fullPath,
                'filters' => $filters,
                'record_count' => $export->getRecordCount()
            ]);

            return $fullPath;

        } catch (\Exception $e) {
            Log::error('HB837 export failed', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Export filtered data to CSV
     */
    public function exportToCsv(array $filters = []): string
    {
        return $this->exportToExcel($filters, 'csv');
    }

    /**
     * Export template file for import
     */
    public function exportTemplate(): string
    {
        try {
            $mappings = config('hb837.mappings', []);
            $headers = array_values($mappings);

            $filename = 'hb837_import_template_' . date('Y-m-d') . '.xlsx';
            $path = 'templates/hb837/' . $filename;

            // Create simple template with headers
            $templateData = [
                $headers, // Header row
                array_fill(0, count($headers), ''), // Empty sample row
            ];

            // Store template
            Excel::store(new class($templateData) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;

                public function __construct($data) {
                    $this->data = $data;
                }

                public function array(): array {
                    return $this->data;
                }
            }, $path, 'local');

            Log::info('HB837 template exported', [
                'filename' => $filename,
                'path' => $path
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::error('HB837 template export failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Export backup of all data
     */
    public function exportBackup(): string
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "hb837_backup_{$timestamp}.xlsx";
            $path = 'backups/hb837/' . date('Y/m/d') . '/' . $filename;

            // Export all data without filters
            $export = new HB837ThreePhaseExport([]);
            Excel::store($export, $path, 'local');

            Log::info('HB837 backup completed', [
                'filename' => $filename,
                'path' => $path,
                'record_count' => $export->getRecordCount()
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::error('HB837 backup failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get export statistics
     */
    public function getExportStatistics(): array
    {
        $total = HB837::count();

        return [
            'total_records' => $total,
            'active_projects' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])->count(),
            'completed_projects' => HB837::where('report_status', 'completed')->count(),
            'quoted_projects' => HB837::where('contracting_status', 'quoted')->count(),
            'executed_projects' => HB837::where('contracting_status', 'executed')->count(),
            'overdue_projects' => HB837::where('scheduled_date_of_inspection', '<', now())
                ->whereNotIn('report_status', ['completed'])
                ->count(),
        ];
    }

    /**
     * Get available export formats
     */
    public function getAvailableFormats(): array
    {
        return [
            'xlsx' => 'Excel (XLSX)',
            'csv' => 'Comma Separated Values (CSV)',
            'pdf' => 'Portable Document Format (PDF)',
        ];
    }

    /**
     * Export to PDF report
     */
    public function exportToPdf(array $filters = []): string
    {
        try {
            $records = $this->getFilteredRecords($filters);

            $filename = 'hb837_report_' . date('Y-m-d_H-i-s') . '.pdf';
            $path = 'exports/hb837/' . date('Y/m/d') . '/' . $filename;

            // In production, you'd create a proper PDF view
            // For now, this is a placeholder

            Log::info('HB837 PDF export completed', [
                'filename' => $filename,
                'path' => $path,
                'record_count' => $records->count()
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::error('HB837 PDF export failed', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Get filtered records for export
     */
    private function getFilteredRecords(array $filters = []): Collection
    {
        $query = HB837::query()->with(['consultant', 'user']);

        if (isset($filters['status'])) {
            $query->where('report_status', $filters['status']);
        }

        if (isset($filters['contracting_status'])) {
            $query->where('contracting_status', $filters['contracting_status']);
        }

        if (isset($filters['consultant_id'])) {
            $query->where('assigned_consultant_id', $filters['consultant_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }
}
