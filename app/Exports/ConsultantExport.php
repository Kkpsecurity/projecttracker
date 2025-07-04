<?php 
namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ConsultantExport implements FromCollection, WithHeadings, WithTitle
{
    protected bool $included_files;

    public function __construct(bool $included_files = false)
    {
        $this->included_files = $included_files;
    }

    public function collection(): Collection
    {
        $data = $this->included_files
            ? $this->consultantsWithFiles()
            : $this->consultantsOnly();

        \Log::info('Exporting consultants: ' . $data->count());

        return $data;
    }

    protected function consultantsOnly(): Collection
    {
        return DB::table('consultants')->get()->map(fn($row) => [
            $row->id,
            $row->first_name,
            $row->last_name,
            $row->email,
            $row->dba_company_name,
            $row->mailing_address,
            $row->fcp_expiration_date,
            $row->assigned_light_meter,
            $row->lm_nist_expiration_date,
            $row->subcontractor_bonus_rate,
            $row->notes,
            $row->created_at,
            $row->updated_at,
        ]);
    }

    protected function consultantsWithFiles(): Collection
    {
        return DB::table('consultants')
            ->leftJoin('consultant_files', 'consultants.id', '=', 'consultant_files.consultant_id')
            ->select(
                'consultants.id',
                'consultants.first_name',
                'consultants.last_name',
                'consultants.email',
                'consultants.dba_company_name',
                'consultants.mailing_address',
                'consultants.fcp_expiration_date',
                'consultants.assigned_light_meter',
                'consultants.lm_nist_expiration_date',
                'consultants.subcontractor_bonus_rate',
                'consultants.notes',
                'consultants.created_at',
                'consultants.updated_at',
                'consultant_files.file_type',
                'consultant_files.original_filename',
                'consultant_files.file_path',
                'consultant_files.file_size'
            )
            ->get()
            ->map(fn($row) => [
                $row->id,
                $row->first_name,
                $row->last_name,
                $row->email,
                $row->dba_company_name,
                $row->mailing_address,
                $row->fcp_expiration_date,
                $row->assigned_light_meter,
                $row->lm_nist_expiration_date,
                $row->subcontractor_bonus_rate,
                $row->notes,
                $row->created_at,
                $row->updated_at,
                $row->file_type,
                $row->original_filename,
                $row->file_path,
                $row->file_size,
            ]);
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'DBA Company Name',
            'Mailing Address',
            'FCP Expiration Date',
            'Assigned Light Meter',
            'LM NIST Expiration Date',
            'Subcontractor Bonus Rate',
            'Notes',
            'Created At',
            'Updated At',
        ];

        if ($this->included_files) {
            $headings[] = 'File Type';
            $headings[] = 'Original Filename';
            $headings[] = 'File Path';
            $headings[] = 'File Size';
        }

        return $headings;
    }

    public function title(): string
    {
        return 'consultants';
    }
}
