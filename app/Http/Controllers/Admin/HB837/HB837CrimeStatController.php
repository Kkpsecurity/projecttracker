<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\HB837CrimeStat;
use App\Services\HB837\HB837CrimeStatSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class HB837CrimeStatController extends Controller
{
    /**
     * Update crime stats payload + review state for a given HB837 record.
     *
     * AJAX endpoint used by the HB837 edit tab.
     */
    public function update(Request $request, HB837 $hb837): JsonResponse
    {
        $validated = $request->validate([
            'report_title' => 'nullable|string|max:255',
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date|after_or_equal:period_start',
            'crime_risk' => 'nullable|string|max:255',
            'is_reviewed' => 'nullable|boolean',
            'offenses' => 'nullable|array',
            'offenses.*.label' => 'nullable|string|max:255',
            'offenses.*.category' => 'nullable|string|max:64',
            'offenses.*.count' => 'nullable|integer|min:0',
            'offenses.*.rate_per_1000' => 'nullable|numeric|min:0',
        ]);

        $crimeStat = HB837CrimeStat::query()->firstOrNew(['hb837_id' => $hb837->id]);

        $existingStats = is_array($crimeStat->stats ?? null) ? $crimeStat->stats : [];
        $stats = HB837CrimeStatSchema::normalize($existingStats) ?? config('hb837.crime_stats_schema_v1');

        // Column-level fields (fast querying)
        $crimeStat->report_title = $validated['report_title'] ?? $crimeStat->report_title;
        $crimeStat->period_start = $validated['period_start'] ?? $crimeStat->period_start;
        $crimeStat->period_end = $validated['period_end'] ?? $crimeStat->period_end;
        $crimeStat->crime_risk = $validated['crime_risk'] ?? $crimeStat->crime_risk;

        // Mirror edits into JSON payload
        Arr::set($stats, 'report.title', $crimeStat->report_title);
        Arr::set($stats, 'report.period.start', $crimeStat->period_start ? (string) $crimeStat->period_start : null);
        Arr::set($stats, 'report.period.end', $crimeStat->period_end ? (string) $crimeStat->period_end : null);
        Arr::set($stats, 'summary.crime_risk', $crimeStat->crime_risk);

        if (array_key_exists('offenses', $validated)) {
            Arr::set($stats, 'tables.offenses', $validated['offenses'] ?? []);
        }

        // Validate/normalize final payload
        $stats = HB837CrimeStatSchema::validateV1($stats, [
            'hb837_file_id' => $crimeStat->hb837_file_id,
            'filename' => Arr::get($stats, 'source.filename'),
            'sha256' => Arr::get($stats, 'source.sha256'),
        ]);

        $crimeStat->stats = $stats;

        $isReviewed = (bool) ($validated['is_reviewed'] ?? false);
        $crimeStat->is_reviewed = $isReviewed;

        if ($isReviewed) {
            $crimeStat->reviewed_by = Auth::id();
            $crimeStat->reviewed_at = now();
        } else {
            $crimeStat->reviewed_by = null;
            $crimeStat->reviewed_at = null;
        }

        $crimeStat->save();

        return response()->json([
            'success' => true,
            'message' => 'Crime stats saved successfully.',
            'crime_stat' => [
                'id' => $crimeStat->id,
                'is_reviewed' => (bool) $crimeStat->is_reviewed,
                'reviewed_at' => optional($crimeStat->reviewed_at)->toDateTimeString(),
            ],
        ]);
    }
}
