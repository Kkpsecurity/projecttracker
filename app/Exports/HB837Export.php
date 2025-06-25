<?php

namespace App\Exports;

use App\Models\HB837;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HB837Export implements FromCollection, WithHeadings, ShouldAutoSize
{

   /**
     * @var array
     */
    protected $included_files;

    public function __construct(bool $included_files = false)
    {
        $this->included_files = $included_files;
    }


    public function collection(): Collection
    {
        return HB837::from('hb837 as hb837s')
            ->select([
                'hb837s.address',
                'hb837s.city',
                'hb837s.county',
                'hb837s.state',
                'hb837s.zip',
                'hb837s.phone',
                'hb837s.property_manager_name',
                'hb837s.property_manager_email',
                'hb837s.regional_manager_name',
                'hb837s.regional_manager_email',
                'hb837s.report_submitted',
                'hb837s.agreement_submitted',
                'hb837s.owner_id',
                'owners.name as owner_name',
                'hb837s.property_name',
                'hb837s.property_type',
                'hb837s.units',
                'hb837s.management_company',
                'hb837s.securitygauge_crime_risk',
                'hb837s.assigned_consultant_id',
                'consultants.first_name as consultant_first_name',
                'consultants.last_name as consultant_last_name',
                'hb837s.scheduled_date_of_inspection',
                'hb837s.report_status',
                'hb837s.contracting_status',
                'hb837s.macro_client',
                'hb837s.macro_contact',
                'hb837s.macro_email',
                'hb837s.quoted_price',
                'hb837s.sub_fees_estimated_expenses',
                'hb837s.project_net_profit',
                'hb837s.billing_req_sent',
                'hb837s.financial_notes',
                'hb837s.consultant_notes',
                'hb837s.notes'
            ])
            ->leftJoin('owners', 'hb837s.owner_id', '=', 'owners.id')
            ->leftJoin('consultants', 'hb837s.assigned_consultant_id', '=', 'consultants.id')
            ->get()
            ->map(function ($row) {
                return [
                    $row->report_status,
                    $row->contracting_status,
                    $row->property_name,
                    $row->property_type,
                    $row->units ?: 0,
                    $row->address,
                    $row->city,
                    $row->county,
                    $row->state,
                    $row->zip,
                    $row->phone,
                    $row->management_company,
                    $row->property_manager_name,
                    $row->property_manager_email,
                    $row->regional_manager_name,
                    $row->regional_manager_email,
                    $row->owner_name,
                    trim("{$row->consultant_first_name} {$row->consultant_last_name}") ?: null,
                    $row->scheduled_date_of_inspection,
                    $row->report_submitted,
                    $row->agreement_submitted,
                    $row->billing_req_sent,
                    $row->securitygauge_crime_risk,
                    $row->quoted_price ?: 0,
                    $row->sub_fees_estimated_expenses ?: 0,
                    $row->project_net_profit ?: 0,
                    $row->macro_client,
                    $row->macro_contact,
                    $row->macro_email,
                    $row->financial_notes,
                    $row->consultant_notes,
                    $row->notes,
                ];
            });

    }

    public function headings(): array
    {
        return [
            'Report Status',
            'Contracting Status',
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
            'Property Manager Name',
            'Property Manager Email',
            'Regional Manager Name',
            'Regional Manager Email',
            'Owner Name',
            'Consultant Name',
            'Scheduled Date of Inspection',
            'Report Submitted',
            'Agreement Submitted',
            'Billing Req Sent',
            'SecurityGauge Crime Risk',
            'Quoted Price',
            'Sub Fees Estimated Expenses',
            'Project Net Profit',
            'Macro Client',
            'Macro Contact',
            'Macro Email',
            'Financial Notes',
            'Consultant Notes',
            'Notes'
        ];
    }
}
