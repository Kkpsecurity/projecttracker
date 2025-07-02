<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\PlotGroup;
use App\Models\HB837;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlotGroupController extends Controller
{
    /**
     * Display plot groups management interface
     */
    public function index()
    {
        $plotGroups = PlotGroup::with(['creator', 'plots'])
                              ->withCount('plots')
                              ->orderBy('name')
                              ->get();

        // Get macro clients for the dropdown
        $macroClients = HB837::whereNotNull('macro_client')
                            ->where('macro_client', '!=', '')
                            ->distinct()
                            ->pluck('macro_client')
                            ->sort()
                            ->values();

        // Get available plots (not in any group or available for multiple groups)
        $availablePlots = Plot::with(['address', 'hb837'])
                             ->whereNotNull('coordinates_latitude')
                             ->whereNotNull('coordinates_longitude')
                             ->get();

        return view('admin.plot-groups.index', compact('plotGroups', 'macroClients', 'availablePlots'));
    }

    /**
     * Store a new plot group
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $plotGroup = PlotGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#3498db',
            'created_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Plot group created successfully!',
                'plot_group' => $plotGroup->load('creator'),
            ]);
        }

        return redirect()->route('admin.plot-groups.index')
                        ->with('success', 'Plot group created successfully!');
    }

    /**
     * Update a plot group
     */
    public function update(Request $request, PlotGroup $plotGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
        ]);

        $plotGroup->update($request->only(['name', 'description', 'color', 'is_active']));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Plot group updated successfully!',
                'plot_group' => $plotGroup->fresh(),
            ]);
        }

        return redirect()->route('admin.plot-groups.index')
                        ->with('success', 'Plot group updated successfully!');
    }

    /**
     * Delete a plot group
     */
    public function destroy(PlotGroup $plotGroup)
    {
        $plotGroup->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Plot group deleted successfully!',
            ]);
        }

        return redirect()->route('admin.plot-groups.index')
                        ->with('success', 'Plot group deleted successfully!');
    }

    /**
     * Add plots to a group
     */
    public function addPlots(Request $request, PlotGroup $plotGroup)
    {
        $request->validate([
            'plot_ids' => 'required|array',
            'plot_ids.*' => 'exists:plots,id',
        ]);

        foreach ($request->plot_ids as $index => $plotId) {
            $plotGroup->plots()->syncWithoutDetaching([
                $plotId => [
                    'sort_order' => $index + 1,
                    'notes' => $request->notes[$plotId] ?? null,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($request->plot_ids) . ' plots added to group successfully!',
        ]);
    }

    /**
     * Remove plots from a group
     */
    public function removePlots(Request $request, PlotGroup $plotGroup)
    {
        $request->validate([
            'plot_ids' => 'required|array',
            'plot_ids.*' => 'exists:plots,id',
        ]);

        $plotGroup->plots()->detach($request->plot_ids);

        return response()->json([
            'success' => true,
            'message' => count($request->plot_ids) . ' plots removed from group successfully!',
        ]);
    }

    /**
     * Get plots for a specific macro client
     */
    public function getMacroClientPlots(Request $request)
    {
        $request->validate([
            'macro_client' => 'required|string',
        ]);

        try {
            // Get all HB837 projects for this macro client
            $hb837Projects = HB837::where('macro_client', $request->macro_client)->get();

            // Get plots associated with these projects
            $plots = Plot::with(['address', 'hb837'])
                        ->whereIn('hb837_id', $hb837Projects->pluck('id'))
                        ->whereNotNull('coordinates_latitude')
                        ->whereNotNull('coordinates_longitude')
                        ->get();

            // Get project addresses that don't have plots yet
            $projectAddresses = [];
            foreach ($hb837Projects as $project) {
                if ($project->address && !$plots->where('hb837_id', $project->id)->count()) {
                    $projectAddresses[] = [
                        'project_name' => $project->property_name,
                        'address' => $project->full_address,
                        'macro_client' => $project->macro_client,
                        'hb837_id' => $project->id,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'macro_client' => $request->macro_client,
                'plots' => $plots,
                'project_addresses' => $projectAddresses,
                'stats' => [
                    'total_projects' => $hb837Projects->count(),
                    'plots_found' => $plots->count(),
                    'project_addresses' => count($projectAddresses),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading macro client plots: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get plot group data for map display
     */
    public function getGroupPlots(PlotGroup $plotGroup)
    {
        $plots = $plotGroup->plots()
                          ->with(['address', 'hb837'])
                          ->whereNotNull('coordinates_latitude')
                          ->whereNotNull('coordinates_longitude')
                          ->get();

        return response()->json([
            'success' => true,
            'plot_group' => $plotGroup,
            'plots' => $plots,
            'stats' => [
                'total_plots' => $plotGroup->plots_count,
                'mapped_plots' => $plots->count(),
            ],
        ]);
    }
}
