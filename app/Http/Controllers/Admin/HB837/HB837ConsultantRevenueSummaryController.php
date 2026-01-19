<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class HB837ConsultantRevenueSummaryController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->toDateString();
        }

        $validated = validator(
            ['start_date' => $startDate, 'end_date' => $endDate],
            ['start_date' => ['required', 'date'], 'end_date' => ['required', 'date', 'after_or_equal:start_date']]
        )->validate();

        $rows = $this->getSummaryRows(
            Carbon::parse($validated['start_date'])->toDateString(),
            Carbon::parse($validated['end_date'])->toDateString()
        );

        return view('admin.hb837.reports.consultant-revenue-summary', [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'rows' => $rows,
        ]);
    }

    public function export(Request $request, string $format)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = Carbon::parse($validated['start_date'])->toDateString();
        $endDate = Carbon::parse($validated['end_date'])->toDateString();

        $rows = $this->getSummaryRows($startDate, $endDate);

        $headings = [
            'Consultant Name',
            'No. of Completed Projects',
            'Gross Revenue',
            'Estimated Expenses',
            'Net Revenue',
            'Average Report Completion Time (Days)',
        ];

        $data = array_map(function ($row) {
            return [
                $row['consultant_name'],
                $row['completed_projects'],
                $row['gross_revenue'],
                $row['estimated_expenses'],
                $row['net_revenue'],
                $row['avg_completion_days'],
            ];
        }, $rows);

        $filenameBase = 'hb837_consultant_revenue_summary_' . $startDate . '_to_' . $endDate;

        $export = new class($headings, $data) implements FromArray, WithHeadings {
            public function __construct(private array $headings, private array $data)
            {
            }

            public function headings(): array
            {
                return $this->headings;
            }

            public function array(): array
            {
                return $this->data;
            }
        };

        if ($format === 'csv') {
            return ExcelFacade::download($export, $filenameBase . '.csv', Excel::CSV);
        }

        return ExcelFacade::download($export, $filenameBase . '.xlsx');
    }

    /**
     * Returns rows shaped for display/export.
     *
     * Date range is based on report delivery date (hb837.report_submitted).
     */
    private function getSummaryRows(string $startDate, string $endDate): array
    {
        $results = DB::table('hb837')
            ->join('consultants', 'hb837.assigned_consultant_id', '=', 'consultants.id')
            ->where('hb837.report_status', '=', 'completed')
            ->whereNotNull('hb837.report_submitted')
            ->whereBetween('hb837.report_submitted', [$startDate, $endDate])
            ->groupBy('consultants.id', 'consultants.first_name', 'consultants.last_name')
            ->orderBy('consultants.first_name')
            ->orderBy('consultants.last_name')
            ->selectRaw(
                "consultants.id as consultant_id, " .
                "CONCAT(consultants.first_name, ' ', consultants.last_name) as consultant_name, " .
                "COUNT(hb837.id) as completed_projects, " .
                "COALESCE(SUM(hb837.quoted_price), 0) as gross_revenue, " .
                "COALESCE(SUM(hb837.sub_fees_estimated_expenses), 0) as estimated_expenses, " .
                "COALESCE(SUM(hb837.quoted_price), 0) - COALESCE(SUM(hb837.sub_fees_estimated_expenses), 0) as net_revenue, " .
                "AVG(CASE " .
                    "WHEN hb837.scheduled_date_of_inspection IS NOT NULL " .
                    "AND hb837.report_submitted IS NOT NULL " .
                    "AND YEAR(hb837.scheduled_date_of_inspection) >= 1980 " .
                    "AND YEAR(hb837.report_submitted) >= 1980 " .
                    "AND hb837.scheduled_date_of_inspection <= CURDATE() " .
                    "AND hb837.report_submitted <= CURDATE() " .
                    "AND hb837.report_submitted >= hb837.scheduled_date_of_inspection " .
                    "AND DATEDIFF(hb837.report_submitted, hb837.scheduled_date_of_inspection) <= 365 " .
                    "THEN DATEDIFF(hb837.report_submitted, hb837.scheduled_date_of_inspection) " .
                "END) as avg_completion_days"
            )
            ->get();

        return $results
            ->map(function ($row) {
                return [
                    'consultant_id' => (int) $row->consultant_id,
                    'consultant_name' => (string) $row->consultant_name,
                    'completed_projects' => (int) $row->completed_projects,
                    'gross_revenue' => (float) $row->gross_revenue,
                    'estimated_expenses' => (float) $row->estimated_expenses,
                    'net_revenue' => (float) $row->net_revenue,
                    'avg_completion_days' => $row->avg_completion_days === null ? null : (float) $row->avg_completion_days,
                ];
            })
            ->values()
            ->all();
    }
}
