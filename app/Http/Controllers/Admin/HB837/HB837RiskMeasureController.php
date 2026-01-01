<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\HB837RiskMeasure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HB837RiskMeasureController extends Controller
{
    private function allowedSections(): array
    {
        return (array) config('hb837.risk_measure_sections', []);
    }

    private function allowedCbRanks(): array
    {
        return (array) config('hb837.risk_measure_cb_ranks', []);
    }

    public function index(HB837 $hb837): JsonResponse
    {
        $measures = $hb837->riskMeasures()
            ->orderBy('section')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'measures' => $measures,
        ]);
    }

    public function store(Request $request, HB837 $hb837): JsonResponse
    {
        $sections = $this->allowedSections();
        $cbRanks = $this->allowedCbRanks();

        $validated = $request->validate([
            'section' => empty($sections)
                ? 'required|string|max:10'
                : 'required|string|in:' . implode(',', $sections),
            'measure_no' => 'nullable|integer|min:1|max:999',
            'cb_rank' => empty($cbRanks)
                ? 'nullable|string|max:5'
                : 'nullable|string|in:' . implode(',', $cbRanks),
            'measure' => 'required|string',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        $measure = HB837RiskMeasure::create([
            'hb837_id' => $hb837->getKey(),
            'created_by' => Auth::id(),
            'section' => $validated['section'],
            'measure_no' => $validated['measure_no'] ?? null,
            'cb_rank' => $validated['cb_rank'] ?? null,
            'measure' => $validated['measure'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Risk measure created.',
            'measure' => $measure,
        ]);
    }

    public function update(Request $request, HB837 $hb837, HB837RiskMeasure $measure): JsonResponse
    {
        if ((int) $measure->hb837_id !== (int) $hb837->getKey()) {
            abort(404);
        }

        $sections = $this->allowedSections();
        $cbRanks = $this->allowedCbRanks();

        $validated = $request->validate([
            'section' => empty($sections)
                ? 'required|string|max:10'
                : 'required|string|in:' . implode(',', $sections),
            'measure_no' => 'nullable|integer|min:1|max:999',
            'cb_rank' => empty($cbRanks)
                ? 'nullable|string|max:5'
                : 'nullable|string|in:' . implode(',', $cbRanks),
            'measure' => 'required|string',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        $measure->fill([
            'section' => $validated['section'],
            'measure_no' => $validated['measure_no'] ?? null,
            'cb_rank' => $validated['cb_rank'] ?? null,
            'measure' => $validated['measure'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $measure->save();

        return response()->json([
            'message' => 'Risk measure updated.',
            'measure' => $measure,
        ]);
    }

    public function destroy(HB837 $hb837, HB837RiskMeasure $measure): JsonResponse
    {
        if ((int) $measure->hb837_id !== (int) $hb837->getKey()) {
            abort(404);
        }

        $measure->delete();

        return response()->json([
            'message' => 'Risk measure deleted.',
        ]);
    }
}
