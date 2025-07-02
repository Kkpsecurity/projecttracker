<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MockPlotsController extends Controller
{
    /**
     * Get mock plots data for DataTables AJAX when database is unavailable
     */
    public function datatable(Request $request)
    {
        // Mock data for testing DataTables functionality
        $mockPlots = [
            [
                'id' => 1,
                'plot_name' => 'Test Plot 1',
                'address' => '123 Main St, City, State',
                'coordinates' => '40.7128, -74.0060',
                'hb837' => '<a href="#">Project Alpha</a>',
                'actions' => '<div class="btn-group btn-group-sm">
                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-success btn-sm"><i class="fas fa-map-marker-alt"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </div>'
            ],
            [
                'id' => 2,
                'plot_name' => 'Test Plot 2',
                'address' => '456 Oak Ave, City, State',
                'coordinates' => '40.7589, -73.9851',
                'hb837' => '<a href="#">Project Beta</a>',
                'actions' => '<div class="btn-group btn-group-sm">
                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-success btn-sm"><i class="fas fa-map-marker-alt"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </div>'
            ],
            [
                'id' => 3,
                'plot_name' => 'Test Plot 3',
                'address' => '789 Pine St, City, State',
                'coordinates' => 'Not set',
                'hb837' => 'Not linked',
                'actions' => '<div class="btn-group btn-group-sm">
                    <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-success btn-sm"><i class="fas fa-map-marker-alt"></i></button>
                    <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </div>'
            ]
        ];

        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';

        // Filter data based on search
        $filteredData = $mockPlots;
        if (!empty($search)) {
            $filteredData = array_filter($mockPlots, function($plot) use ($search) {
                return stripos($plot['plot_name'], $search) !== false ||
                       stripos($plot['address'], $search) !== false;
            });
        }

        // Paginate
        $paginatedData = array_slice($filteredData, $start, $length);

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => count($mockPlots),
            'recordsFiltered' => count($filteredData),
            'data' => array_values($paginatedData)
        ]);
    }
}
