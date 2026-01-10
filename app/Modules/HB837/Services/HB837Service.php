<?php

namespace App\Modules\HB837\Services;

use App\Models\HB837;
use App\Models\Consultant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class HB837Service
{
    /**
     * Get all HB837 records with filtering
     */
    public function getAll(array $filters = []): Collection
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

        return $query->get();
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(): array
    {
        return [
            'active' => HB837::whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
                ->where('contracting_status', 'executed')->count(),
            'quoted' => HB837::whereIn('contracting_status', ['quoted', 'started'])->count(),
            'completed' => HB837::where('report_status', 'completed')->count(),
            'closed' => HB837::where('contracting_status', 'closed')->count(),
            'total' => HB837::count(),
            'overdue' => HB837::where('scheduled_date_of_inspection', '<', now())
                ->whereNotIn('report_status', ['completed'])
                ->count(),
        ];
    }

    /**
     * Create a new HB837 record
     */
    public function create(array $data): HB837
    {
        return HB837::create($data);
    }

    /**
     * Update an existing HB837 record
     */
    public function update(HB837 $hb837, array $data): HB837
    {
        $hb837->update($data);
        return $hb837->fresh();
    }

    /**
     * Delete an HB837 record
     */
    public function delete(HB837 $hb837): bool
    {
        return $hb837->delete();
    }

    /**
     * Duplicate an HB837 record
     */
    public function duplicate(HB837 $hb837): HB837
    {
        $data = $hb837->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);

        // Reset status fields for duplicate
        $data['report_status'] = 'not-started';
        $data['contracting_status'] = 'quoted';
        $data['property_name'] = $data['property_name'] . ' (Copy)';

        return HB837::create($data);
    }

    /**
     * Calculate priority score for a property
     */
    public function calculatePriorityScore(HB837 $hb837): int
    {
        $score = 0;

        // Overdue inspections get priority
        if ($hb837->scheduled_date_of_inspection &&
            $hb837->scheduled_date_of_inspection < now() &&
            $hb837->report_status !== 'completed') {
            $score += 2;
        }

        // High crime risk areas get priority
        if (in_array($hb837->securitygauge_crime_risk, ['High', 'Very High'])) {
            $score += 1;
        }

        // High value properties get priority
        if ($hb837->quoted_price && $hb837->quoted_price > 5000) {
            $score += 1;
        }

        return min($score, 3); // Cap at 3 (urgent)
    }

    /**
     * Get consultants for assignment
     */
    public function getAvailableConsultants(): Collection
    {
        return Consultant::where('status', 'active')->get();
    }
}
