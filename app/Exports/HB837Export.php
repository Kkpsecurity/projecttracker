<?php

namespace App\Exports;

use App\Models\HB837;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HB837Export implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $tab;
    protected $format;

    public function __construct($tab = 'active', $format = 'xlsx')
    {
        $this->tab = $tab;
        $this->format = $format;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = HB837::query()->with(['consultant', 'user']);
        
        // Apply tab filters similar to controller
        switch ($this->tab) {
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

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Property Name',
            'Management Company',
            'Owner Name',
            'Property Type',
            'Units',
            'Address',
            'City',
            'County', 
            'State',
            'ZIP',
            'Phone',
            'Assigned Consultant',
            'Scheduled Date',
            'Report Status',
            'Contracting Status',
            'Quoted Price',
            'Sub Fees/Expenses',
            'Billing Req Sent',
            'Report Submitted',
            'Agreement Submitted',
            'Project Net Profit',
            'SecurityGauge Crime Risk',
            'Macro Client',
            'Macro Contact',
            'Macro Email',
            'Property Manager Name',
            'Property Manager Email',
            'Regional Manager Name',
            'Regional Manager Email',
            'Notes',
            'Financial Notes',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param mixed $hb837
     * @return array
     */
    public function map($hb837): array
    {
        return [
            $hb837->id,
            $hb837->property_name,
            $hb837->management_company,
            $hb837->owner_name,
            $hb837->property_type,
            $hb837->units,
            $hb837->address,
            $hb837->city,
            $hb837->county,
            $hb837->state,
            $hb837->zip,
            $hb837->phone,
            $hb837->consultant ? $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name : '',
            $hb837->scheduled_date_of_inspection ? \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection)->format('Y-m-d') : '',
            $hb837->report_status,
            $hb837->contracting_status,
            $hb837->quoted_price,
            $hb837->sub_fees_estimated_expenses,
            $hb837->billing_req_sent ? \Carbon\Carbon::parse($hb837->billing_req_sent)->format('Y-m-d') : '',
            $hb837->report_submitted ? \Carbon\Carbon::parse($hb837->report_submitted)->format('Y-m-d') : '',
            $hb837->agreement_submitted ? \Carbon\Carbon::parse($hb837->agreement_submitted)->format('Y-m-d') : '',
            $hb837->project_net_profit,
            $hb837->securitygauge_crime_risk,
            $hb837->macro_client,
            $hb837->macro_contact,
            $hb837->macro_email,
            $hb837->property_manager_name,
            $hb837->property_manager_email,
            $hb837->regional_manager_name,
            $hb837->regional_manager_email,
            $hb837->notes,
            $hb837->financial_notes,
            $hb837->created_at ? \Carbon\Carbon::parse($hb837->created_at)->format('Y-m-d H:i:s') : '',
            $hb837->updated_at ? \Carbon\Carbon::parse($hb837->updated_at)->format('Y-m-d H:i:s') : ''
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'HB837 ' . ucfirst($this->tab) . ' Export';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
