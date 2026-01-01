<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\PlotAddress;
use App\Models\PlotGroup;
use App\Models\HB837;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoogleMapsController extends Controller
{
    /**
     * Display the main maps interface
     */
    public function index()
    {
        // Get all plots with coordinates
        $plots = Plot::with(['address', 'hb837'])
                    ->whereNotNull('coordinates_latitude')
                    ->whereNotNull('coordinates_longitude')
                    ->get();

        // Get unique macro clients for dropdown
        $macroClients = HB837::whereNotNull('macro_client')
                            ->where('macro_client', '!=', '')
                            ->distinct()
                            ->pluck('macro_client')
                            ->sort()
                            ->values();

        // Get active plot groups for dropdown
        $plotGroups = PlotGroup::where('is_active', true)
            ->withCount('plots')
            ->orderBy('name')
            ->get();

        // Get statistics
        $stats = [
            'total_plots' => Plot::count(),
            'mapped_plots' => Plot::whereNotNull('coordinates_latitude')
                                 ->whereNotNull('coordinates_longitude')
                                 ->count(),
            'total_projects' => HB837::count(),
            'total_addresses' => PlotAddress::count(),
            'total_plot_groups' => PlotGroup::where('is_active', true)->count(),
        ];

        return view('admin.maps.index', compact('plots', 'stats', 'macroClients', 'plotGroups'));
    }

    /**
     * Show plot details
     */
    public function showPlot($id)
    {
        $plot = Plot::with(['address', 'hb837'])->findOrFail($id);

        return view('admin.maps.plot-detail', compact('plot'));
    }

    /**
     * Get plots data for AJAX
     */
    public function getPlotsData(Request $request)
    {
        $query = Plot::with(['address', 'hb837'])
                    ->whereNotNull('coordinates_latitude')
                    ->whereNotNull('coordinates_longitude');

        // Filter by HB837 status if provided
        if ($request->has('status') && $request->status) {
            $query->whereHas('hb837', function($q) use ($request) {
                $q->where('report_status', $request->status);
            });
        }

        // Filter by subdivision if provided
        if ($request->has('subdivision') && $request->subdivision) {
            $query->where('subdivision_name', 'LIKE', '%' . $request->subdivision . '%');
        }

        $plots = $query->get();

        // Format data for Google Maps
        $mapData = $plots->map(function($plot) {
            return [
                'id' => $plot->id,
                'name' => $plot->plot_name,
                'lat' => (float) $plot->coordinates_latitude,
                'lng' => (float) $plot->coordinates_longitude,
                'full_location' => $plot->full_location,
                'hb837_id' => $plot->hb837_id,
                'hb837_status' => $plot->hb837 ? $plot->hb837->report_status : null,
                'address' => $plot->address ? $plot->address->full_address : null,
                'info_window' => view('admin.maps.partials.info-window', compact('plot'))->render()
            ];
        });

        return response()->json($mapData);
    }

    /**
     * Create new plot with coordinates
     */
    public function createPlot(Request $request)
    {
        $request->validate([
            'plot_name' => 'required|string|max:255',
            'coordinates_latitude' => 'required|numeric|between:-90,90',
            'coordinates_longitude' => 'required|numeric|between:-180,180',
            'hb837_id' => 'nullable|exists:hb837,id',
            'lot_number' => 'nullable|string|max:50',
            'block_number' => 'nullable|string|max:50',
            'subdivision_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);


        dd($request->all());
        $plot = Plot::create($request->all());

        // Create address if provided
        if ($request->has('street_address')) {
            $plot->address()->create([
                'street_address' => $request->street_address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
            ]);
        }

        return response()->json([
            'success' => true,
            'plot' => $plot->load(['address', 'hb837']),
            'message' => 'Plot created successfully'
        ]);
    }

    /**
     * Update plot coordinates
     */
    public function updatePlotCoordinates(Request $request, $id)
    {
        $request->validate([
            'coordinates_latitude' => 'required|numeric|between:-90,90',
            'coordinates_longitude' => 'required|numeric|between:-180,180',
        ]);

        $plot = Plot::findOrFail($id);
        $plot->update([
            'coordinates_latitude' => $request->coordinates_latitude,
            'coordinates_longitude' => $request->coordinates_longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plot coordinates updated successfully'
        ]);
    }

    /**
     * Get nearby plots
     */
    public function getNearbyPlots(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:50', // km
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 5; // Default 5km radius

        // Haversine formula for finding nearby plots
        $plots = Plot::selectRaw("
            *,
            (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(coordinates_latitude)) *
                    cos(radians(coordinates_longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(coordinates_latitude))
                )
            ) AS distance
        ", [$lat, $lng, $lat])
        ->whereNotNull('coordinates_latitude')
        ->whereNotNull('coordinates_longitude')
        ->having('distance', '<', $radius)
        ->orderBy('distance')
        ->with(['address', 'hb837'])
        ->get();

        return response()->json([
            'success' => true,
            'plots' => $plots,
            'center' => ['lat' => $lat, 'lng' => $lng],
            'radius' => $radius
        ]);
    }

    /**
     * Export plots data
     */
    public function exportPlots(Request $request)
    {
        $plots = Plot::with(['address', 'hb837'])->get();

        $csvData = [];
        $csvData[] = [
            'Plot Name', 'Latitude', 'Longitude', 'Lot Number', 'Block Number',
            'Subdivision', 'Address', 'HB837 ID', 'HB837 Status', 'Description'
        ];

        foreach ($plots as $plot) {
            $csvData[] = [
                $plot->plot_name,
                $plot->coordinates_latitude,
                $plot->coordinates_longitude,
                $plot->lot_number,
                $plot->block_number,
                $plot->subdivision_name,
                $plot->address ? $plot->address->full_address : '',
                $plot->hb837_id,
                $plot->hb837 ? $plot->hb837->report_status : '',
                $plot->description,
            ];
        }

        $filename = 'plots_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Create plot from address input
     */
    public function createPlotFromAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            // If coordinates not provided, try to geocode the address
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            if (!$latitude || !$longitude) {
                // You can integrate with Google Geocoding API here
                // For now, we'll return an error asking for coordinates
                return response()->json([
                    'success' => false,
                    'message' => 'Please click on the map to set coordinates for this address.',
                    'needs_coordinates' => true,
                ]);
            }

            // Create the plot
            $plot = Plot::create([
                'plot_name' => 'Plot from Address Input',
                'description' => 'Created from address: ' . $request->address,
                'coordinates_latitude' => $latitude,
                'coordinates_longitude' => $longitude,
            ]);

            // Create the address
            $addressParts = $this->parseAddress($request->address);
            $plot->address()->create([
                'street_address' => $addressParts['street'] ?? $request->address,
                'city' => $addressParts['city'] ?? '',
                'state' => $addressParts['state'] ?? '',
                'zip_code' => $addressParts['zip'] ?? '',
                'country' => $addressParts['country'] ?? 'US',
                'plot_id' => $plot->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plot created successfully from address!',
                'plot' => $plot->load('address'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating plot: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get plots for a specific macro client
     */
    public function getMacroClientPlots(Request $request)
    {
        $request->validate([
            'macro_client' => 'required|string|max:255',
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

            // Also get project addresses that don't have plots yet
            $projectAddresses = [];
            foreach ($hb837Projects as $project) {
                if ($project->address && !$plots->where('hb837_id', $project->id)->count()) {
                    $projectAddresses[] = [
                        'id' => 'project_' . $project->id,
                        'type' => 'project_address',
                        'project_name' => $project->project_name,
                        'address' => $project->address,
                        'city' => $project->city,
                        'state' => $project->state,
                        'zip_code' => $project->zip_code,
                        'macro_client' => $project->macro_client,
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
     * Get plots for a specific plot group
     */
    public function getPlotGroupPlots(Request $request)
    {
        try {
            $request->validate([
                'plot_group_id' => 'required|exists:plot_groups,id',
            ]);

            $plotGroup = PlotGroup::with(['plots.address', 'plots.hb837'])
                ->findOrFail($request->plot_group_id);

            // Get plots that belong to this group and have coordinates
            $plots = $plotGroup->plots()
                ->whereNotNull('coordinates_latitude')
                ->whereNotNull('coordinates_longitude')
                ->with(['address', 'hb837'])
                ->get();

            // Format plots for map display
            $formattedPlots = $plots->map(function ($plot) {
                return [
                    'id' => $plot->id,
                    'plot_name' => $plot->plot_name,
                    'coordinates_latitude' => $plot->coordinates_latitude,
                    'coordinates_longitude' => $plot->coordinates_longitude,
                    'plot_address' => $plot->address,
                    'hb837' => $plot->hb837,
                ];
            });

            return response()->json([
                'success' => true,
                'plot_group' => [
                    'id' => $plotGroup->id,
                    'name' => $plotGroup->name,
                    'description' => $plotGroup->description,
                    'color' => $plotGroup->color,
                ],
                'plots' => $formattedPlots,
                'stats' => [
                    'total_plots' => $plotGroup->plots()->count(),
                    'mapped_plots' => $plots->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading plot group: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new plot and add it to a plot group
     */
    public function createPlotInGroup(Request $request)
    {
        try {
            $request->validate([
                'plot_group_id' => 'required|exists:plot_groups,id',
                'address' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'plot_name' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();

            // Create the plot first
            $plot = Plot::create([
                'plot_name' => $request->plot_name ?: 'Plot at ' . $request->address,
                'coordinates_latitude' => $request->latitude,
                'coordinates_longitude' => $request->longitude,
            ]);

            // Create the plot address and associate it with the plot
            $addressData = $this->parseAddress($request->address);
            $plotAddress = PlotAddress::create([
                'street_address' => $addressData['street'] ?? $request->address,
                'city' => $addressData['city'] ?? null,
                'state' => $addressData['state'] ?? null,
                'zip_code' => $addressData['zip'] ?? null,
                'country' => $addressData['country'] ?? 'US',
                'plot_id' => $plot->id,
            ]);

            // Add plot to the group
            $plotGroup = PlotGroup::findOrFail($request->plot_group_id);
            $nextSortOrder = $plotGroup->plots()->max('plot_group_plots.sort_order') + 1;

            $plotGroup->plots()->attach($plot->id, [
                'sort_order' => $nextSortOrder,
                'notes' => 'Added via maps interface',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Load the created plot with relationships
            $plot->load(['address', 'hb837']);

            return response()->json([
                'success' => true,
                'message' => 'Plot created and added to group successfully',
                'plot' => [
                    'id' => $plot->id,
                    'plot_name' => $plot->plot_name,
                    'coordinates_latitude' => $plot->coordinates_latitude,
                    'coordinates_longitude' => $plot->coordinates_longitude,
                    'plot_address' => $plot->address,
                    'hb837' => $plot->hb837,
                ],
                'plot_group' => [
                    'id' => $plotGroup->id,
                    'name' => $plotGroup->name,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating plot: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse address string into components
     */
    private function parseAddress($address)
    {
        // Simple address parsing - can be enhanced with more sophisticated logic
        $parts = explode(',', $address);
        $result = [];

        if (count($parts) >= 1) {
            $result['street'] = trim($parts[0]);
        }
        if (count($parts) >= 2) {
            $result['city'] = trim($parts[1]);
        }
        if (count($parts) >= 3) {
            $stateZip = trim($parts[2]);
            if (preg_match('/^(.+?)\s+(\d{5}(-\d{4})?)$/', $stateZip, $matches)) {
                $result['state'] = trim($matches[1]);
                $result['zip'] = $matches[2];
            } else {
                $result['state'] = $stateZip;
            }
        }
        if (count($parts) >= 4) {
            $result['country'] = trim($parts[3]);
        }

        return $result;
    }
}
