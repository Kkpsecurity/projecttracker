<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\PlotAddress;
use Illuminate\Http\Request;

class GoogleMapsController extends Controller
{
    /**
     * Display the Google Maps view.
     *
     * @return \Illuminate\View\View
     */
    public function mapPlots()
    {
        $plots = Plot::active()->with('plotAddresses')->get();
        $plot_id = request()->query('selectedPlotId');

        if ($plot_id) {
            $selectedPlot = Plot::with('plotAddresses')->find($plot_id);
        } else {
            $selectedPlot = new Plot;
        }

        // Get distinct macro clients for dropdown
        $macroClients = \App\Models\HB837::whereNotNull('macro_client')
            ->distinct()
            ->pluck('macro_client')
            ->sort()
            ->values();

        // Return the view for Google Maps
        return view('admin.hb837.google-maps', compact('plots', 'selectedPlot', 'macroClients'));
    }

    /**
     * Store a newly created plot.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'plot_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'plot_type' => 'required|in:custom,prospect,client',
            'client_contact_name' => 'nullable|string|max:255',
            'client_contact_email' => 'nullable|email|max:255',
            'client_contact_phone' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Set default values
        $validatedData['is_active'] = $validatedData['is_active'] ?? true;

        $plot = Plot::create($validatedData);

        return redirect()->route('admin.mapplots.index')
            ->with('success', 'Plot created successfully.');
    }

    public function addAddressToPlot(Request $request)
    {
        $validatedData = $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:255',
            'property_type' => 'nullable|string|max:255',
            'property_value' => 'nullable|numeric',
            'square_footage' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $plotAddress = PlotAddress::create([
            'plot_id' => $validatedData['plot_id'],
            'location_name' => $validatedData['address'],
            'latitude' => $validatedData['lat'],
            'longitude' => $validatedData['lng'],
            'street_address' => $validatedData['street_address'] ?? null,
            'city' => $validatedData['city'] ?? null,
            'state' => $validatedData['state'] ?? null,
            'zip_code' => $validatedData['zip_code'] ?? null,
            'country' => 'USA',
            'property_type' => $validatedData['property_type'] ?? null,
            'property_value' => $validatedData['property_value'] ?? null,
            'square_footage' => $validatedData['square_footage'] ?? null,
            'description' => $validatedData['description'] ?? null,
            'status' => PlotAddress::STATUS_ACTIVE,
        ]);

        return response()->json([
            'success' => true,
            'address' => $plotAddress->load('plot'),
            'message' => 'Address added successfully!',
        ]);
    }

    /**
     * Load addresses based on the selected plot or macro client.
     *
     * @method GET
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadAddresses(Request $request)
    {
        $plotId = $request->query('selectedPlotId');
        $macroClient = $request->query('selectedMacroClient');

        // Return an empty array if neither selection is provided.
        if (! $plotId && ! $macroClient) {
            return response()->json(['addresses' => []]);
        }

        // Process based on available parameter.
        $addresses = $plotId
            ? $this->getFormattedPlotAddresses($plotId)
            : $this->getFormattedMacroAddresses($macroClient);

        return response()->json(['addresses' => $addresses]);
    }

    /**
     * Helper method: Get formatted addresses for a given plot.
     *
     * @param  int  $plotId
     * @return \Illuminate\Support\Collection
     */
    protected function getFormattedPlotAddresses($plotId)
    {
        $plot = Plot::with('plotAddresses')->findOrFail($plotId);

        return $plot->plotAddresses->map(function ($address) {
            return [
                'id' => $address->id,
                'location_name' => $address->location_name,
                'address' => $address->full_address ?: $address->location_name,
                'latitude' => (float) $address->latitude,
                'longitude' => (float) $address->longitude,
                'property_type' => $address->property_type,
                'property_value' => $address->formatted_value,
                'status' => $address->status,
                'description' => $address->description,
                'type' => 'plot', // Distinguish from macro client addresses
            ];
        });
    }

    /**
     * Helper method: Get formatted addresses for a given macro client.
     *
     * Note: Macro addresses might not have latitude and longitude. In such cases, those fields will be null.
     *
     * @param  string  $macro
     * @return \Illuminate\Support\Collection
     */
    protected function getFormattedMacroAddresses($macro)
    {
        $properties = \App\Models\HB837::where('macro_client', $macro)
            ->whereNotNull('address')
            ->get(['id', 'property_name', 'address', 'city', 'state', 'zip']);

        return $properties->map(function ($property) {
            return [
                'id' => $property->id,
                'location_name' => $property->property_name,
                'address' => trim($property->address.' '.$property->city.' '.$property->state.' '.$property->zip),
                'latitude' => null, // no lat field in HB837
                'longitude' => null, // no lng field in HB837
                'property_type' => 'existing', // Existing property from HB837
                'status' => 'active',
                'type' => 'macro', // Distinguish from plot addresses
            ];
        });
    }

    /**
     * Store a new address for a plot.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPlotAddress(Request $request)
    {
        $validatedData = $request->validate([
            'plot_id' => 'required|exists:plots,id',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $plotAddress = new PlotAddress;
        $plotAddress->plot_id = $validatedData['plot_id'];
        $plotAddress->location_name = $validatedData['address'];
        $plotAddress->latitude = $validatedData['lat'];
        $plotAddress->longitude = $validatedData['lng'];
        $plotAddress->save();

        return response()->json(['success' => true]);
    }

    public function getMacroClientProperties(Request $request)
    {
        $macroClient = $request->query('macroClient');

        if (! $macroClient) {
            return response()->json(['properties' => []]);
        }

        $properties = \App\Models\HB837::where('macro_client', $macroClient)
            ->whereNotNull('address')
            ->get(['property_name', 'address', 'city', 'state', 'zip']);

        return response()->json(['properties' => $properties]);
    }

    /**
     * Delete a specific plot address (AJAX version).
     *
     * @param  int  $plotAddressId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAddressFromPlot($plotAddressId)
    {
        try {
            $plotAddress = PlotAddress::findOrFail($plotAddressId);
            $plotAddress->delete();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully!',
                'deleted_id' => $plotAddressId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete address. '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a plot and all its addresses.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePlotAndAddresses($id)
    {
        $plot = Plot::findOrFail($id);

        // Delete all associated addresses first
        $plot->plotAddresses()->delete();
        $plot->delete();

        return redirect()->route('admin.mapplots.index')
            ->with('success', 'Plot and addresses deleted successfully!');
    }

    /**
     * Update an existing plot.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $plot = Plot::findOrFail($id);

        $validatedData = $request->validate([
            'plot_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'plot_type' => 'required|in:custom,prospect,client',
            'client_contact_name' => 'nullable|string|max:255',
            'client_contact_email' => 'nullable|email|max:255',
            'client_contact_phone' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $plot->update($validatedData);

        return response()->json([
            'success' => true,
            'plot' => $plot,
            'message' => 'Plot updated successfully!',
        ]);
    }

    /**
     * Convert a custom plot to a client plot.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertToClient(Request $request, $id)
    {
        $plot = Plot::findOrFail($id);

        $validatedData = $request->validate([
            'client_contact_name' => 'required|string|max:255',
            'client_contact_email' => 'nullable|email|max:255',
            'client_contact_phone' => 'nullable|string|max:255',
        ]);

        $plot->update([
            'plot_type' => Plot::TYPE_CLIENT,
            'client_contact_name' => $validatedData['client_contact_name'],
            'client_contact_email' => $validatedData['client_contact_email'],
            'client_contact_phone' => $validatedData['client_contact_phone'],
        ]);

        return response()->json([
            'success' => true,
            'plot' => $plot,
            'message' => 'Plot converted to client successfully!',
        ]);
    }

    /**
     * Export plot data for presentation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportPlotData($id)
    {
        $plot = Plot::with('plotAddresses')->findOrFail($id);

        $exportData = [
            'plot' => [
                'name' => $plot->plot_name,
                'description' => $plot->description,
                'type' => $plot->plot_type,
                'contact' => [
                    'name' => $plot->client_contact_name,
                    'email' => $plot->client_contact_email,
                    'phone' => $plot->client_contact_phone,
                ],
                'created_at' => $plot->created_at->format('Y-m-d H:i:s'),
            ],
            'addresses' => $plot->plotAddresses->map(function ($address) {
                return [
                    'location_name' => $address->location_name,
                    'full_address' => $address->full_address,
                    'coordinates' => [
                        'latitude' => (float) $address->latitude,
                        'longitude' => (float) $address->longitude,
                    ],
                    'property_details' => [
                        'type' => $address->property_type,
                        'value' => $address->formatted_value,
                        'square_footage' => $address->square_footage,
                        'status' => $address->status,
                    ],
                    'description' => $address->description,
                ];
            }),
            'summary' => [
                'total_properties' => $plot->plotAddresses->count(),
                'total_value' => $plot->plotAddresses->sum('property_value'),
                'active_properties' => $plot->plotAddresses->where('status', PlotAddress::STATUS_ACTIVE)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
            'filename' => "plot_{$plot->id}_{$plot->plot_name}_".now()->format('Y-m-d').'.json',
        ]);
    }

    /**
     * Get plot statistics for dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlotStatistics()
    {
        $stats = [
            'total_plots' => Plot::count(),
            'active_plots' => Plot::active()->count(),
            'plots_by_type' => [
                'custom' => Plot::ofType(Plot::TYPE_CUSTOM)->count(),
                'prospect' => Plot::ofType(Plot::TYPE_PROSPECT)->count(),
                'client' => Plot::ofType(Plot::TYPE_CLIENT)->count(),
            ],
            'total_addresses' => PlotAddress::count(),
            'addresses_by_status' => [
                'active' => PlotAddress::byStatus(PlotAddress::STATUS_ACTIVE)->count(),
                'inactive' => PlotAddress::byStatus(PlotAddress::STATUS_INACTIVE)->count(),
                'pending' => PlotAddress::byStatus(PlotAddress::STATUS_PENDING)->count(),
                'sold' => PlotAddress::byStatus(PlotAddress::STATUS_SOLD)->count(),
            ],
            'total_property_value' => PlotAddress::sum('property_value'),
        ];

        return response()->json($stats);
    }
}
