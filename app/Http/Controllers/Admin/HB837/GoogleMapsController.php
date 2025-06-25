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
        $plots = Plot::all();
        $plot_id = request()->query('selectedPlotId');

        if ($plot_id) {
            $selectedPlot = Plot::with('plotAddresses')->find($plot_id);
        } else {
            $selectedPlot = new Plot();
        }

        // Return the view for Google Maps
        return view('admin.hb837.google-maps', compact('plots', 'selectedPlot'));
    }

    /**
     * Store a newly created plot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'plot_name' => 'required',
        ]);

        Plot::create($validatedData);

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
        ]);

        $plotAddress = new PlotAddress();
        $plotAddress->plot_id = $validatedData['plot_id'];
        $plotAddress->location_name = $validatedData['address'];
        $plotAddress->latitude = $validatedData['lat'];
        $plotAddress->longitude = $validatedData['lng'];
        $plotAddress->save();

        return response()->json(['success' => true]);
    }

    /**
     * Load addresses based on the selected plot or macro client.
     *
     * @param \Illuminate\Http\Request $request
     * @method GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadAddresses(Request $request)
    {
        $plotId = $request->query('selectedPlotId');
        $macroClient = $request->query('selectedMacroClient');

        // Return an empty array if neither selection is provided.
        if (!$plotId && !$macroClient) {
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
     * @param int $plotId
     * @return \Illuminate\Support\Collection
     */
    protected function getFormattedPlotAddresses($plotId)
    {
        $plot = Plot::with('plotAddresses')->findOrFail($plotId);
        return $plot->plotAddresses->map(function ($address) {
            return [
                'id' => $address->id,
                'location_name' => "Plot: {$address->id}",
                'address' => $address->location_name,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
            ];
        });
    }

    /**
     * Helper method: Get formatted addresses for a given macro client.
     *
     * Note: Macro addresses might not have latitude and longitude. In such cases, those fields will be null.
     *
     * @param string $macro
     * @return \Illuminate\Support\Collection
     */
    protected function getFormattedMacroAddresses($macro)
    {
        $properties = \App\Models\HB837::where('macro_client', $macro)
            ->whereNotNull('address')
            ->get(['property_name', 'address', 'city', 'state', 'zip']);

        return $properties->map(function ($property) {
            return [
                'id' => $property->id, // no id field
                'location_name' => $property->property_name,
                'address' => $property->address . " " . $property->city . " " . $property->state . " " . $property->zip,
                'latitude' => null, // no lat field
                'longitude' => null, // no lng field
            ];
        });
    }


    /**
     * Store a new address for a plot.
     *
     * @param  \Illuminate\Http\Request  $request
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

        $plotAddress = new PlotAddress();
        $plotAddress->plot_id = $validatedData['plot_id'];
        $plotAddress->location_name = $validatedData['address'];
        $plotAddress->latitude = $validatedData['lat'];
        $plotAddress->longitude = $validatedData['lng'];
        $plotAddress->save();

        return response()->json(['success' => true]);
    }

    public function getMacroClientProperties (Request $request)
    {
        $macroClient = $request->query('macroClient');

        if (!$macroClient) {
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
     * @param int $plotAddressId
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
                'deleted_id' => $plotAddressId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete address. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a plot and all its addresses.
     *
     * @param int $id
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
}
