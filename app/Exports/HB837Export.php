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
    protected $collection;

    public function __construct($tab = null, $format = 'xlsx')
    {
        // Prefer explicit argument, then query string, then default to 'all'
        if ($tab === null) {
            $tab = request()->query('tab', 'all');
        }

        // Debugging line to check the full URL from the browser
        // You can get the full URL using request()->fullUrl()
        // Example:

        $this->tab = $tab;
        $this->format = $format;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return cached collection if already built
        if ($this->collection !== null) {
            return $this->collection;
        }

        $query = HB837::query()->with(['consultant', 'user']);
        
        // Apply tab filters similar to controller
        switch ($this->tab) {
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
            case 'all':
                // No additional filters for all
                break;
        }

        // Cache the collection
        $this->collection = $query->get();

        return $this->collection;
    }    /**
         * @return array
         */
    public function headings(): array
    {
        return [
            'id',
            'user_id',
            'property_name',
            'management_company',
            'owner_name',
            'property_type',
            'units',
            'address',
            'city',
            'county',
            'state',
            'zip',
            'phone',
            'assigned_consultant',
            'scheduled_date_of_inspection',
            'report_status',
            'contracting_status',
            'quoted_price',
            'sub_fees_estimated_expenses',
            'billing_req_submitted',
            'report_submitted',
            'agreement_submitted',
            'project_net_profit',
            'securitygauge_crime_risk',
            'macro_client',
            'macro_contact',
            'macro_email',
            'property_manager_name',
            'property_manager_email',
            'regional_manager_name',
            'regional_manager_email',
            'notes',
            'financial_notes',
            'consultant_notes',
            'created_at',
            'updated_at'
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
            $hb837->user ? $hb837->user->name : ($hb837->user ? $hb837->user->first_name . ' ' . $hb837->user->last_name : 'Unknown User'),
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
            $hb837->consultant ? $hb837->consultant->first_name . ' ' . $hb837->consultant->last_name : 'Unassigned',
            $hb837->scheduled_date_of_inspection ? \Carbon\Carbon::parse($hb837->scheduled_date_of_inspection)->format('Y-m-d') : '',
            $hb837->report_status,
            $hb837->contracting_status,
            $hb837->quoted_price,
            $hb837->sub_fees_estimated_expenses,
            $hb837->billing_req_submitted ? \Carbon\Carbon::parse($hb837->billing_req_submitted)->format('Y-m-d') : '',
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
            $hb837->consultant_notes,
            $hb837->created_at ? \Carbon\Carbon::parse($hb837->created_at)->format('Y-m-d H:i:s') : '',
            $hb837->updated_at ? \Carbon\Carbon::parse($hb837->updated_at)->format('Y-m-d H:i:s') : ''
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // Get the count of records for this tab
        $count = $this->collection()->count();

        return 'HB837 ' . ucfirst($this->tab) . ' Export (' . $count . ' records)';
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