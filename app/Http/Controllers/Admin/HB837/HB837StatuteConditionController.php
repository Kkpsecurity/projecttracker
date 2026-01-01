<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\HB837StatuteCondition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HB837StatuteConditionController extends Controller
{
    public function index(HB837 $hb837): JsonResponse
    {
        $conditions = $hb837->statuteConditions()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'conditions' => $conditions,
        ]);
    }

    public function upsertMany(Request $request, HB837 $hb837): JsonResponse
    {
        $validated = $request->validate([
            'conditions' => ['required', 'array', 'min:1'],
            'conditions.*.condition_key' => ['required', 'string', 'max:64'],
            'conditions.*.status' => ['nullable', 'string', 'max:32'],
            'conditions.*.observations' => ['nullable', 'string'],
            'conditions.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $actorId = Auth::id();
        $incoming = collect($validated['conditions']);

        DB::transaction(function () use ($incoming, $hb837, $actorId) {
            foreach ($incoming as $row) {
                HB837StatuteCondition::query()->updateOrCreate(
                    [
                        'hb837_id' => $hb837->id,
                        'condition_key' => $row['condition_key'],
                    ],
                    [
                        'created_by' => $actorId,
                        'status' => $row['status'] ?? null,
                        'observations' => $row['observations'] ?? null,
                        'sort_order' => isset($row['sort_order']) ? (int) $row['sort_order'] : 0,
                    ]
                );
            }
        });

        $conditions = $hb837->statuteConditions()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'conditions' => $conditions,
        ]);
    }
}
