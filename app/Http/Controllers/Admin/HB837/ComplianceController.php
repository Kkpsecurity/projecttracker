<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\HB837CrimeStat;
use App\Models\HB837File;
use App\Models\HB837Finding;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class ComplianceController extends Controller
{
    public function index(): View
    {
        $findingStatuses = (array) Config::get('hb837.finding_statuses', []);
        $openFindingStatuses = array_values(array_intersect($findingStatuses, ['new', 'in-progress']));

        $totalProjects = HB837::query()->count();

        $totalFindings = HB837Finding::query()->count();
        $openFindings = empty($openFindingStatuses)
            ? HB837Finding::query()->count()
            : HB837Finding::query()->whereIn('status', $openFindingStatuses)->count();

        $projectsWithFindings = HB837Finding::query()->distinct('hb837_id')->count('hb837_id');

        $crimeReportUploads = HB837File::query()->where('file_category', 'crime_report')->count();
        $projectsWithCrimeReport = HB837File::query()
            ->where('file_category', 'crime_report')
            ->distinct('hb837_id')
            ->count('hb837_id');

        $projectsMissingCrimeReport = HB837::query()
            ->whereDoesntHave('files', function ($query) {
                $query->where('file_category', 'crime_report');
            })
            ->count();

        $crimeStatsTotal = HB837CrimeStat::query()->count();
        $crimeStatsPendingReview = HB837CrimeStat::query()->where('is_reviewed', false)->count();
        $crimeStatsReviewed = $crimeStatsTotal - $crimeStatsPendingReview;

        return view('admin.hb837.compliance.index', [
            'pageTitle' => 'HB 837 Compliance',
            'stats' => [
                'total_projects' => $totalProjects,
                'total_findings' => $totalFindings,
                'open_findings' => $openFindings,
                'projects_with_findings' => $projectsWithFindings,
                'crime_report_uploads' => $crimeReportUploads,
                'projects_with_crime_report' => $projectsWithCrimeReport,
                'projects_missing_crime_report' => $projectsMissingCrimeReport,
                'crime_stats_total' => $crimeStatsTotal,
                'crime_stats_pending_review' => $crimeStatsPendingReview,
                'crime_stats_reviewed' => $crimeStatsReviewed,
            ],
        ]);
    }
}
