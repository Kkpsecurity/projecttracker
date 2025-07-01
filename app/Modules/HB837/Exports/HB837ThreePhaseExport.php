<?php

namespace App\Modules\HB837\Exports;

use App\Models\HB837;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HB837ThreePhaseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
{
    private $filters;
    private $recordCount = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get the collection of records to export
     */
    public function collection()
    {
        $query = HB837::query()->with(['consultant', 'user']);

        // Apply filters
        if (isset($this->filters['status'])) {
            $query->where('report_status', $this->filters['status']);
        }

        if (isset($this->filters['contracting_status'])) {
            $query->where('contracting_status', $this->filters['contracting_status']);
        }

        if (isset($this->filters['consultant_id'])) {
            $query->where('assigned_consultant_id', $this->filters['consultant_id']);
        }

        if (isset($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->where('created_at', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['city'])) {
            $query->where('city', 'LIKE', '%' . $this->filters['city'] . '%');
        }

        if (isset($this->filters['state'])) {
            $query->where('state', $this->filters['state']);
        }

        $collection = $query->get();
        $this->recordCount = $collection->count();

        return $collection;
    }

    /**
     * Define the headings for the export
     */
    public function headings(): array
    {
        return [
            'ID',
            'Property Name',
            'Property Type',
            'Units',
            'Address',
            'City',
            'County',
            'State',
            'Zip',
            'Phone',
            'Management Company',
            'Owner Name',
            'Property Manager Name',
            'Property Manager Email',
            'Regional Manager Name',
            'Regional Manager Email',
            'Assigned Consultant',
            'Report Status',
            'Contracting Status',
            'Scheduled Date of Inspection',
            'Report Submitted',
            'Agreement Submitted',
            'Billing Req Sent',
            'Quoted Price',
            'Sub Fees/Estimated Expenses',
            'Project Net Profit',
            'SecurityGauge Crime Risk',
            'Notes',
            'Financial Notes',
            'Macro Client',
            'Macro Contact',
            'Macro Email',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Map each row of data
     */
    public function map($hb837): array
    {
        return [
            $hb837->id,
            $hb837->property_name,
            $hb837->property_type,
            $hb837->units,
            $hb837->address,
            $hb837->city,
            $hb837->county,
            $hb837->state,
            $hb837->zip,
            $hb837->phone,
            $hb837->management_company,
            $hb837->owner_name,
            $hb837->property_manager_name,
            $hb837->property_manager_email,
            $hb837->regional_manager_name,
            $hb837->regional_manager_email,
            $hb837->consultant ? $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name : '',
            $hb837->report_status,
            $hb837->contracting_status,
            $hb837->scheduled_date_of_inspection ? $hb837->scheduled_date_of_inspection->format('Y-m-d') : '',
            $hb837->report_submitted ? $hb837->report_submitted->format('Y-m-d') : '',
            $hb837->agreement_submitted ? $hb837->agreement_submitted->format('Y-m-d') : '',
            $hb837->billing_req_sent ? $hb837->billing_req_sent->format('Y-m-d') : '',
            $hb837->quoted_price,
            $hb837->sub_fees_estimated_expenses,
            $hb837->project_net_profit,
            $hb837->securitygauge_crime_risk,
            $hb837->notes,
            $hb837->financial_notes,
            $hb837->macro_client,
            $hb837->macro_contact,
            $hb837->macro_email,
            $hb837->created_at ? $hb837->created_at->format('Y-m-d H:i:s') : '',
            $hb837->updated_at ? $hb837->updated_at->format('Y-m-d H:i:s') : '',
        ];
    }

    /**
     * Define column formatting
     */
    public function columnFormats(): array
    {
        return [
            'T' => NumberFormat::FORMAT_DATE_YYYYMMDD,      // Scheduled Date
            'U' => NumberFormat::FORMAT_DATE_YYYYMMDD,      // Report Submitted
            'V' => NumberFormat::FORMAT_DATE_YYYYMMDD,      // Agreement Submitted
            'W' => NumberFormat::FORMAT_DATE_YYYYMMDD,      // Billing Req Sent
            'X' => NumberFormat::FORMAT_CURRENCY_USD,       // Quoted Price
            'Y' => NumberFormat::FORMAT_CURRENCY_USD,       // Sub Fees
            'Z' => NumberFormat::FORMAT_CURRENCY_USD,       // Net Profit
            'AG' => NumberFormat::FORMAT_DATE_DATETIME,     // Created At
            'AH' => NumberFormat::FORMAT_DATE_DATETIME,     // Updated At
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E3F2FD',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the number of records exported
     */
    public function getRecordCount(): int
    {
        return $this->recordCount;
    }
}
