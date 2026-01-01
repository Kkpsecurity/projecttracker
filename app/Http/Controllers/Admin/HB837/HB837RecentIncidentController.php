<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\HB837RecentIncident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HB837RecentIncidentController extends Controller
{
    public function index(HB837 $hb837): JsonResponse
    {
        $incidents = $hb837->recentIncidents()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'incidents' => $incidents,
        ]);
    }

    public function store(Request $request, HB837 $hb837): JsonResponse
    {
        $validated = $request->validate([
            'incident_date' => 'nullable|string|max:60',
            'summary' => 'required|string',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        $incident = HB837RecentIncident::create([
            'hb837_id' => $hb837->getKey(),
            'created_by' => Auth::id(),
            'incident_date' => $validated['incident_date'] ?? null,
            'summary' => $validated['summary'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Recent incident created.',
            'incident' => $incident,
        ]);
    }

    public function update(Request $request, HB837 $hb837, HB837RecentIncident $incident): JsonResponse
    {
        if ((int) $incident->hb837_id !== (int) $hb837->getKey()) {
            abort(404);
        }

        $validated = $request->validate([
            'incident_date' => 'nullable|string|max:60',
            'summary' => 'required|string',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        $incident->fill([
            'incident_date' => $validated['incident_date'] ?? null,
            'summary' => $validated['summary'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);
        $incident->save();

        return response()->json([
            'message' => 'Recent incident updated.',
            'incident' => $incident,
        ]);
    }

    public function destroy(HB837 $hb837, HB837RecentIncident $incident): JsonResponse
    {
        if ((int) $incident->hb837_id !== (int) $hb837->getKey()) {
            abort(404);
        }

        $incident->delete();

        return response()->json([
            'message' => 'Recent incident deleted.',
        ]);
    }
}
