<?php

namespace App\Services\HB837;

use App\Models\HB837;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class HB837ReportService
{
    /**
     * Generate P    /**
     * Apply search filters to query
     */
    private function applySearchFilters($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('property_name', 'ilike', '%' . $search . '%')
                ->orWhere('address', 'ilike', '%' . $search . '%')
                ->orWhere('city', 'ilike', '%' . $search . '%')
                ->orWhere('management_company', 'ilike', '%' . $search . '%');
        });
    }

    /**
     * Export data based on request parameters
     */
    public function export(\Illuminate\Http\Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $format = $request->input('format', 'excel');
        $tab = $request->input('tab', 'all');
        
        $query = HB837::query()->with(['consultant']);
        
        // Apply tab filters
        $this->applyTabFilters($query, $tab);
        
        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        
        $data = $query->get();
        
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($data);
            case 'csv':
                return $this->exportToCsv($data);
            default:
                return $this->exportToExcel($data);
        }
    }

    /**
     * Get Google Maps embed URL for property
     */
    public function getMapsEmbedUrl(HB837 $hb837): \Illuminate\Http\JsonResponse
    {
        try {
            $address = trim($hb837->address . ', ' . $hb837->city . ', ' . $hb837->state . ' ' . $hb837->zip);
            $encodedAddress = urlencode($address);
            
            $embedUrl = "https://www.google.com/maps/embed/v1/place?key=" . 
                       config('services.google.maps_api_key') . 
                       "&q=" . $encodedAddress;
            
            return response()->json([
                'embed_url' => $embedUrl,
                'address' => $address
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate map'], 500);
        }
    }

    /**
     * Export data to Excel format
     */
    private function exportToExcel($data): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $export = new \App\Exports\HB837Export($data);
        $filename = 'hb837_export_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    /**
     * Export data to CSV format
     */
    private function exportToCsv($data): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $export = new \App\Exports\HB837Export($data);
        $filename = 'hb837_export_' . now()->format('Y-m-d_His') . '.csv';
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Export data to PDF format
     */
    private function exportToPdf($data): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.hb837.exports.pdf', compact('data'));
        $filename = 'hb837_export_' . now()->format('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate PDF report for a specific HB837 record
     */
    public function generatePdfReport(HB837 $hb837)
    {
        $hb837->load(['consultant', 'user', 'files']);

        // Prepare Google Maps data
        $mapData = $this->prepareMapData($hb837);

        // Prepare data for PDF
        $data = [
            'hb837' => $hb837,
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
            'generated_by' => Auth::user()->name ?? 'System',
            'map_url' => $mapData['url'],
            'show_map' => $mapData['show'],
            'map_fallback_reason' => $mapData['fallback_reason']
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

        // Generate filename with property name
        $suffix = 'ID' . $hb837->id . '_' . date('Y-m-d');
        $filename = 'HB837_' . $this->createCleanFilename($hb837->property_name, $suffix);

        return $pdf->download($filename);
    }

    /**
     * Generate bulk PDF report for multiple records
     */
    public function generateBulkPdfReport(array $filters = []): \Illuminate\Http\Response
    {
        $tab = $filters['tab'] ?? 'active';
        $search = $filters['search'] ?? '';

        // Get filtered records
        $query = HB837::query()->with(['consultant', 'user']);

        // Apply tab filters
        $this->applyTabFilters($query, $tab);

        // Apply search filter if provided
        if (!empty($search)) {
            $this->applySearchFilter($query, $search);
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
     * Prepare Google Maps data for PDF
     */
    private function prepareMapData(HB837 $hb837): array
    {
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

        return [
            'url' => $mapUrl,
            'show' => $showMap,
            'fallback_reason' => $fallbackReason
        ];
    }

    /**
     * Create a clean filename from property name
     */
    private function createCleanFilename(string $propertyName, string $suffix = '', string $extension = '.pdf'): string
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
     * Apply tab filters to query
     */
    private function applyTabFilters($query, string $tab): void
    {
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
    }

    /**
     * Apply search filter to query
     */
    private function applySearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('property_name', 'ilike', '%' . $search . '%')
                ->orWhere('address', 'ilike', '%' . $search . '%')
                ->orWhere('city', 'ilike', '%' . $search . '%')
                ->orWhere('management_company', 'ilike', '%' . $search . '%');
        });
    }
}
