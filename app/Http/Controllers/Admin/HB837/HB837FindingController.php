<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\HB837Finding;
use App\Models\Plot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HB837FindingController extends Controller
{
    private function allowedCategories(): array
    {
        return (array) config('hb837.finding_categories', []);
    }

    private function allowedSeverities(): array
    {
        return (array) config('hb837.finding_severities', []);
    }

    private function allowedStatuses(): array
    {
        return (array) config('hb837.finding_statuses', []);
    }

    public function index(HB837 $hb837): JsonResponse
    {
        $findings = $hb837->findings()
            ->latest()
            ->get();

        return response()->json([
            'findings' => $findings,
        ]);
    }

    public function store(Request $request, HB837 $hb837): JsonResponse
    {
        $categories = $this->allowedCategories();
        $severities = $this->allowedSeverities();
        $statuses = $this->allowedStatuses();

        $validated = $request->validate([
            'plot_id' => 'nullable|integer',
            'category' => empty($categories) ? 'required|string|max:255' : 'required|string|in:'.implode(',', $categories),
            'severity' => empty($severities) ? 'required|string|max:255' : 'required|string|in:'.implode(',', $severities),
            'location_context' => 'required|string|max:255',
            'description' => 'required|string',
            'recommendation' => 'required|string',
            'status' => empty($statuses) ? 'required|string|max:255' : 'required|string|in:'.implode(',', $statuses),
        ]);

        if (! empty($validated['plot_id'])) {
            $plotExistsForProject = Plot::query()
                ->whereKey($validated['plot_id'])
                ->where('hb837_id', $hb837->getKey())
                ->exists();

            if (! $plotExistsForProject) {
                return response()->json([
                    'message' => 'Invalid plot_id for this HB837 record.',
                ], 422);
            }
        }

        $finding = HB837Finding::create([
            'hb837_id' => $hb837->getKey(),
            'plot_id' => $validated['plot_id'] ?? null,
            'created_by' => Auth::id(),
            'category' => $validated['category'] ?? null,
            'severity' => $validated['severity'] ?? null,
            'location_context' => $validated['location_context'] ?? null,
            'description' => $validated['description'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'status' => $validated['status'],
            'source' => 'manual',
        ]);

        return response()->json([
            'message' => 'Finding created.',
            'finding' => $finding,
        ]);
    }

    public function update(Request $request, HB837 $hb837, HB837Finding $finding): JsonResponse
    {
        if ((int) $finding->hb837_id !== (int) $hb837->getKey()) {
            abort(404);
        }

        $categories = $this->allowedCategories();
        $severities = $this->allowedSeverities();
        $statuses = $this->allowedStatuses();

        $validated = $request->validate([
            'plot_id' => 'nullable|integer',
            'category' => empty($categories) ? 'required|string|max:255' : 'required|string|in:'.implode(',', $categories),
            'severity' => empty($severities) ? 'required|string|max:255' : 'required|string|in:'.implode(',', $severities),
            'location_context' => 'required|string|max:255',
            'description' => 'required|string',
            'recommendation' => 'required|string',
            'status' => empty($statuses) ? 'required|string|max:255' : 'required|string|in:'.implode(',', $statuses),
        ]);

        if (array_key_exists('plot_id', $validated) && ! empty($validated['plot_id'])) {
            $plotExistsForProject = Plot::query()
                ->whereKey($validated['plot_id'])
                ->where('hb837_id', $hb837->getKey())
                ->exists();

            if (! $plotExistsForProject) {
                return response()->json([
                    'message' => 'Invalid plot_id for this HB837 record.',
                ], 422);
            }
        }

        $finding->fill([
            'plot_id' => $validated['plot_id'] ?? null,
            'category' => $validated['category'] ?? null,
            'severity' => $validated['severity'] ?? null,
            'location_context' => $validated['location_context'] ?? null,
            'description' => $validated['description'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'status' => $validated['status'],
        ]);

        $finding->save();

        return response()->json([
            'message' => 'Finding updated.',
            'finding' => $finding,
        ]);
    }

    public function destroy(HB837 $hb837, HB837Finding $finding): JsonResponse
    {
        if ((int) $finding->hb837_id !== (int) $hb837->getKey()) {
            abort(404);
        }

        $finding->delete();

        return response()->json([
            'message' => 'Finding deleted.',
        ]);
    }
}
