<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\PlotAddress;
use App\Models\HB837;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlotsController extends Controller
{
    /**
     * Display a listing of plots
     */
    public function index(Request $request)
    {
        $query = Plot::with(['address', 'hb837']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('plot_name', 'LIKE', "%{$search}%")
                  ->orWhere('subdivision_name', 'LIKE', "%{$search}%")
                  ->orWhere('lot_number', 'LIKE', "%{$search}%")
                  ->orWhere('block_number', 'LIKE', "%{$search}%");
            });
        }

        // Filter by HB837 status
        if ($request->has('hb837_status') && $request->hb837_status) {
            $query->whereHas('hb837', function($q) use ($request) {
                $q->where('report_status', $request->hb837_status);
            });
        }

        // Filter by subdivision
        if ($request->has('subdivision') && $request->subdivision) {
            $query->where('subdivision_name', $request->subdivision);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $plots = $query->paginate(15)->appends($request->query());

        // Get filter options
        $subdivisions = Plot::whereNotNull('subdivision_name')
                           ->distinct()
                           ->pluck('subdivision_name')
                           ->sort();

        $hb837Statuses = ['not-started', 'in-progress', 'in-review', 'completed'];

        return view('admin.plots.index', compact('plots', 'subdivisions', 'hb837Statuses'));
    }

    /**
     * Show the form for creating a new plot
     */
    public function create()
    {
        $hb837Projects = HB837::select('id', 'property_name', 'address', 'city', 'state')
                             ->get();

        return view('admin.plots.create', compact('hb837Projects'));
    }

    /**
     * Store a newly created plot
     */
    public function store(Request $request)
    {
        $request->validate([
            'plot_name' => 'required|string|max:255',
            'hb837_id' => 'nullable|exists:hb837,id',
            'lot_number' => 'nullable|string|max:50',
            'block_number' => 'nullable|string|max:50',
            'subdivision_name' => 'nullable|string|max:255',
            'coordinates_latitude' => 'nullable|numeric|between:-90,90',
            'coordinates_longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',

            // Address fields
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
        ]);

        DB::transaction(function() use ($request) {
            // Create plot
            $plot = Plot::create($request->only([
                'plot_name', 'hb837_id', 'lot_number', 'block_number',
                'subdivision_name', 'coordinates_latitude', 'coordinates_longitude',
                'description'
            ]));

            // Create address if provided
            if ($request->filled('street_address')) {
                $plot->address()->create($request->only([
                    'street_address', 'city', 'state', 'zip_code'
                ]));
            }
        });

        return redirect()->route('admin.plots.index')
                        ->with('success', 'Plot created successfully');
    }

    /**
     * Display the specified plot
     */
    public function show(Plot $plot)
    {
        $plot->load(['address', 'hb837']);

        return view('admin.plots.show', compact('plot'));
    }

    /**
     * Show the form for editing the specified plot
     */
    public function edit(Plot $plot)
    {
        $plot->load('address');

        $hb837Projects = HB837::select('id', 'property_name', 'address', 'city', 'state')
                             ->get();

        return view('admin.plots.edit', compact('plot', 'hb837Projects'));
    }

    /**
     * Update the specified plot
     */
    public function update(Request $request, Plot $plot)
    {
        $request->validate([
            'plot_name' => 'required|string|max:255',
            'hb837_id' => 'nullable|exists:hb837,id',
            'lot_number' => 'nullable|string|max:50',
            'block_number' => 'nullable|string|max:50',
            'subdivision_name' => 'nullable|string|max:255',
            'coordinates_latitude' => 'nullable|numeric|between:-90,90',
            'coordinates_longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',

            // Address fields
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
        ]);

        DB::transaction(function() use ($request, $plot) {
            // Update plot
            $plot->update($request->only([
                'plot_name', 'hb837_id', 'lot_number', 'block_number',
                'subdivision_name', 'coordinates_latitude', 'coordinates_longitude',
                'description'
            ]));

            // Update or create address
            if ($request->filled('street_address')) {
                $plot->address()->updateOrCreate(
                    ['plot_id' => $plot->id],
                    $request->only(['street_address', 'city', 'state', 'zip_code'])
                );
            } elseif ($plot->address) {
                $plot->address->delete();
            }
        });

        return redirect()->route('admin.plots.index')
                        ->with('success', 'Plot updated successfully');
    }

    /**
     * Remove the specified plot
     */
    public function destroy(Plot $plot)
    {
        DB::transaction(function() use ($plot) {
            // Delete address if exists
            if ($plot->address) {
                $plot->address->delete();
            }

            // Delete plot
            $plot->delete();
        });

        return redirect()->route('admin.plots.index')
                        ->with('success', 'Plot deleted successfully');
    }

    /**
     * Get plots for DataTables AJAX
     */
    public function datatable(Request $request)
    {
        $query = Plot::with(['address', 'hb837']);

        return datatables($query)
            ->addColumn('actions', function($plot) {
                return view('admin.plots.partials.actions', compact('plot'))->render();
            })
            ->addColumn('plot_address', function ($plot) {
                return $plot->address; // Return the full address object for frontend rendering
            })
            ->addColumn('status', function ($plot) {
                // Add a default status field since DataTables expects it
                return 'active'; // You can customize this based on your business logic
            })
            ->editColumn('coordinates', function($plot) {
                if ($plot->coordinates_latitude && $plot->coordinates_longitude) {
                    return $plot->coordinates_latitude . ', ' . $plot->coordinates_longitude;
                }
                return 'Not set';
            })
            ->editColumn('address', function($plot) {
                return $plot->address ? $plot->address->full_address : 'Not set';
            })
            ->editColumn('hb837', function($plot) {
                if ($plot->hb837) {
                    return '<a href="' . route('admin.hb837.show', $plot->hb837_id) . '">' .
                        $plot->hb837->property_name . '</a>';
                }
                return 'Not linked';
            })
            ->rawColumns(['actions', 'hb837'])
            ->make(true);
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,export',
            'ids' => 'required|array',
            'ids.*' => 'exists:plots,id'
        ]);

        switch ($request->action) {
            case 'delete':
                return $this->bulkDelete($request->ids);
            case 'export':
                return $this->bulkExport($request->ids);
        }
    }

    /**
     * Bulk delete plots
     */
    private function bulkDelete($ids)
    {
        DB::transaction(function() use ($ids) {
            // Delete associated addresses
            PlotAddress::whereIn('plot_id', $ids)->delete();

            // Delete plots
            Plot::whereIn('id', $ids)->delete();
        });

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' plots deleted successfully'
        ]);
    }

    /**
     * Bulk export plots
     */
    private function bulkExport($ids)
    {
        $plots = Plot::with(['address', 'hb837'])
                    ->whereIn('id', $ids)
                    ->get();

        $csvData = [];
        $csvData[] = [
            'Plot Name', 'Latitude', 'Longitude', 'Lot Number', 'Block Number',
            'Subdivision', 'Address', 'HB837 Property', 'Description'
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
                $plot->hb837 ? $plot->hb837->property_name : '',
                $plot->description,
            ];
        }

        $filename = 'plots_bulk_export_' . date('Y-m-d_H-i-s') . '.csv';

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
}
