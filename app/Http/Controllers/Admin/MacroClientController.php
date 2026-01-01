<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use App\Models\Plot;
use App\Models\Consultant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MacroClientController extends Controller
{
    /**
     * Display the macro clients page
     */
    public function index()
    {
        // Get all macro clients with their property counts
        $macroClients = HB837::whereNotNull('macro_client')
            ->where(
                'macro_client',
                '!=',
                ''
            )
            ->select('macro_client')
            ->distinct()
            ->get()
            ->map(function ($client) {
                $clientProjects = HB837::where('macro_client', $client->macro_client)->get();
                $totalPlots = $clientProjects->sum(function ($project) {
                    return $project->plots()->count();
                });
                
                return (object) [
                    'macro_client' => $client->macro_client,
                    'plots_count' => $totalPlots,
                    'projects_count' => $clientProjects->count(),
                ];
            })
            ->sortBy('macro_client')
            ->values();

        return view('admin.macro-clients.index', compact('macroClients'));
    }

    /**
     * Get client details for AJAX
     */
    public function getClientDetails(Request $request)
    {
        try {
            $clientName = $request->get('client');
            $consultantFilter = $request->get('consultant_filter', 'all');

            if (!$clientName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client name is required'
                ], 400);
            }

            // Get all projects for this client with consultant filtering
            $query = HB837::where('macro_client', $clientName);

            // Apply consultant filter
            if ($consultantFilter === 'with_consultant') {
                $query->whereNotNull('assigned_consultant_id')
                    ->where('assigned_consultant_id', '!=', 0);
            } elseif ($consultantFilter === 'no_consultant') {
                $query->where(function ($q) {
                    $q->whereNull('assigned_consultant_id')
                        ->orWhere('assigned_consultant_id', 0);
                });
            }

            $projects = $query->get();
            
            if ($projects->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for this client with the selected filter'
                ]);
            }

            // Calculate statistics
            $totalPlots = $projects->sum(function ($project) {
                return $project->plots()->count();
            });

            $activeProjects = $projects->filter(function ($project) {
                return in_array($project->report_status, ['not-started', 'underway', 'in-review'])
                    && $project->contracting_status === 'executed';
            })->count();
            $completedProjects = $projects->where('report_status', 'completed')->count();
            $totalValue = $projects->sum('quoted_price');

            // Calculate consultant-specific statistics
            $withConsultant = $projects->filter(function ($project) {
                return $project->assigned_consultant_id && $project->assigned_consultant_id != 0;
            })->count();

            $withoutConsultant = $projects->count() - $withConsultant;

            $clientData = [
                'macro_client' => $clientName,
                'plots_count' => $totalPlots,
                'projects_count' => $projects->count(),
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'total_value' => $totalValue,
                'consultant_stats' => [
                    'with_consultant' => $withConsultant,
                    'without_consultant' => $withoutConsultant,
                    'filter_applied' => $consultantFilter
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $clientData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading client details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client properties data for DataTable
     */
    public function getClientPropertiesData(Request $request)
    {
        $clientName = $request->get('client_filter');
        $statusFilter = $request->get('status_filter', 'all');
        $consultantFilter = $request->get('consultant_filter', 'all');

        if (!$clientName && $consultantFilter !== 'no_consultant') {
            return response()->json(['data' => [], 'debug' => 'No client name provided and not no_consultant filter']);
        }

        try {
            // Debug: Log the incoming parameters
            \Log::info('MacroClient Properties Request', [
                'client_name' => $clientName,
                'status_filter' => $statusFilter,
                'consultant_filter' => $consultantFilter
            ]);

            // Debug: Check what we actually have in the database
            $allCount = HB837::count();
            $statusCounts = HB837::select('report_status', \DB::raw('count(*) as count'))
                ->groupBy('report_status')
                ->get()
                ->pluck('count', 'report_status')
                ->toArray();

            // Special handling for "no consultant" filter - show ALL active properties without consultants
            if ($consultantFilter === 'no_consultant') {
                $query = HB837::with(['plots.address', 'consultant'])
                    ->whereIn('report_status', ['not-started', 'underway', 'in-review']) // Active report statuses
                    ->where('contracting_status', 'executed') // Active contracting status
                    ->where(function ($q) {
                        $q->whereNull('assigned_consultant_id')
                            ->orWhere('assigned_consultant_id', 0);
                    });
                // No client filter for "no consultant" - show from ALL clients
            } elseif ($clientName === 'all') {
                // Show all properties from all clients
                $query = HB837::with(['plots.address', 'consultant']);

                // Apply consultant filter for all clients
                if ($consultantFilter === 'with_consultant') {
                    $query->whereNotNull('assigned_consultant_id')
                        ->where('assigned_consultant_id', '!=', 0);
                }

                // Apply status filter
                $this->applyStatusFilter($query, $statusFilter);

            } else {
                // Normal client-specific filtering
                $query = HB837::with(['plots.address', 'consultant'])
                    ->where('macro_client', $clientName);

                // Apply consultant filter
                if ($consultantFilter === 'with_consultant') {
                    $query->whereNotNull('assigned_consultant_id')
                        ->where('assigned_consultant_id', '!=', 0);
                }

                // Apply status filter
                $this->applyStatusFilter($query, $statusFilter);
            }            // Handle DataTables parameters
            $start = $request->get('start', 0);
            $length = $request->get('length', 25);
            $searchValue = $request->get('search.value', '');

            // Apply search if provided
            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('property_name', 'like', "%{$searchValue}%")
                      ->orWhere('address', 'like', "%{$searchValue}%")
                      ->orWhere('property_type', 'like', "%{$searchValue}%")
                      ->orWhere('report_status', 'like', "%{$searchValue}%");
                });
            }

            // Get total count for pagination
            $totalRecords = $query->count();

            // Apply pagination
            $projects = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $data = [];
            foreach ($projects as $project) {
                // Get the first plot for this project (if any)
                $plot = $project->plots->first();

                // For "no consultant" filter or "all clients", show macro client in property name
                $propertyName = $project->property_name ?: 'Unnamed Property';
                if ($consultantFilter === 'no_consultant' || $clientName === 'all') {
                    $propertyName .= ' (' . $project->macro_client . ')';
                }

                $data[] = [
                    'id' => $project->id,
                    'property_name' => $propertyName,
                    'address' => $this->formatAddress($project),
                    'property_type' => ucfirst($project->property_type ?: 'Unknown'),
                    'report_status' => $project->report_status ?: 'not-started',
                    'assigned_consultant' => $project->consultant ? $project->consultant->full_name : null,
                    'quoted_price' => $project->quoted_price ?: 0,
                    'scheduled_date_of_inspection' => $project->scheduled_date_of_inspection,
                    'actions' => '', // Will be handled by frontend
                ];
            }

            return response()->json([
                'draw' => $request->get('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
                'debug' => [
                    'client_name' => $clientName,
                    'status_filter' => $statusFilter,
                    'consultant_filter' => $consultantFilter,
                    'total_records' => $totalRecords,
                    'data_count' => count($data),
                    'database_stats' => [
                        'total_hb837_records' => $allCount,
                        'status_counts' => $statusCounts
                    ],
                    'query_conditions' => [
                        'no_consultant' => $consultantFilter === 'no_consultant',
                        'all_clients' => $clientName === 'all',
                        'specific_client' => !in_array($clientName, ['all', null, ''])
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading client properties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get macro client plots for map display
     */
    public function getMacroClientPlots(Request $request)
    {
        // For "no consultant" filter or "all clients", we don't require macro_client
        $consultantFilter = $request->get('consultant_filter', 'all');

        if ($consultantFilter !== 'no_consultant' && $request->get('macro_client') !== 'all') {
            $request->validate([
                'macro_client' => 'required|string|max:255',
            ]);
        }

        try {
            $macroClient = $request->get('macro_client');

            // Special handling for "no consultant" filter - show ALL active properties without consultants
            if ($consultantFilter === 'no_consultant') {
                $query = HB837::whereIn('report_status', ['not-started', 'underway', 'in-review']) // Active report statuses
                    ->where('contracting_status', 'executed') // Active contracting status
                    ->where(function ($q) {
                        $q->whereNull('assigned_consultant_id')
                            ->orWhere('assigned_consultant_id', 0);
                    });
                $macroClient = 'All Active Properties (No Consultant)'; // Override display name
            } elseif ($macroClient === 'all') {
                // Show all properties from all clients
                $query = HB837::query();

                // Apply consultant filter for all clients
                if ($consultantFilter === 'with_consultant') {
                    $query->whereNotNull('assigned_consultant_id')
                        ->where('assigned_consultant_id', '!=', 0);
                }
                $macroClient = 'All Clients'; // Override display name
            } else {
                // Normal client-specific filtering
                $query = HB837::where('macro_client', $macroClient);

                // Apply consultant filter
                if ($consultantFilter === 'with_consultant') {
                    $query->whereNotNull('assigned_consultant_id')
                        ->where('assigned_consultant_id', '!=', 0);
                }
                // 'all' consultant filter requires no additional conditions
            }

            $hb837Projects = $query->get();

            // Get plots that already have coordinates
            $plots = Plot::with(['address', 'hb837'])
                        ->whereIn('hb837_id', $hb837Projects->pluck('id'))
                        ->whereNotNull('coordinates_latitude')
                        ->whereNotNull('coordinates_longitude')
                        ->get();

            // Create plot objects for ALL HB837 projects for map display
            $allPlots = collect();

            foreach ($hb837Projects as $project) {
                // Check if this project already has a plot with coordinates
                $existingPlot = $plots->where('hb837_id', $project->getKey())->first();

                if ($existingPlot) {
                    // Use the existing plot with coordinates
                    $allPlots->push($existingPlot);
                } else {
                    // Create a pseudo-plot object for geocoding using HB837 address
                    $addressForGeocoding = $this->formatAddress($project);

                    if ($addressForGeocoding && $addressForGeocoding !== 'No address available') {
                        // For "no consultant" filter or "all clients", include client name in plot name
                        $plotName = $project->property_name;
                        if ($request->get('consultant_filter') === 'no_consultant' || $request->get('macro_client') === 'all') {
                            $plotName .= ' (' . $project->macro_client . ')';
                        }

                        $pseudoPlot = (object) [
                            'id' => 'project_' . $project->getKey(),
                            'plot_name' => $plotName,
                            'coordinates_latitude' => null, // Will be geocoded
                            'coordinates_longitude' => null, // Will be geocoded
                            'address' => (object) [
                                'street_address' => $addressForGeocoding
                            ],
                            'hb837' => $project,
                            'needs_geocoding' => true,
                        ];
                        $allPlots->push($pseudoPlot);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'macro_client' => $macroClient,
                'plots' => $allPlots,
                'stats' => [
                    'total_projects' => $hb837Projects->count(),
                    'plots_with_coordinates' => $plots->count(),
                    'projects_for_geocoding' => $allPlots->where('needs_geocoding', true)->count(),
                    'total_mappable' => $allPlots->count(),
                    'projects_with_addresses' => $hb837Projects->filter(function ($p) {
                        return $this->formatAddress($p) !== 'No address available';
                    })->count(),
                ],
                'debug' => [
                    'sample_addresses' => $hb837Projects->take(3)->map(function ($p) {
                        return [
                            'project_name' => $p->property_name,
                            'formatted_address' => $this->formatAddress($p),
                            'raw_address' => $p->address,
                            'city' => $p->city,
                            'state' => $p->state,
                            'zip' => $p->zip,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading macro client plots: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format address for display
     */
    private function formatAddress($project)
    {
        $addressParts = array_filter([
            $project->address,
            $project->city,
            $project->state,
            $project->zip
        ]);
        
        return implode(', ', $addressParts) ?: 'No address available';
    }

    /**
     * Apply status filter to query based on tab values
     */
    private function applyStatusFilter($query, $statusFilter)
    {
        switch ($statusFilter) {
            case 'active':
                // Active tab mapping: report_status in ['not-started', 'underway', 'in-review'] AND contracting_status = 'executed'
                $query->whereIn('report_status', ['not-started', 'underway', 'in-review'])
                    ->where('contracting_status', 'executed');
                break;
            case 'quoted':
                // Quoted tab mapping: report_status in ['not-started', 'underway', 'in-review'] AND contracting_status in ['quoted', 'started']
                $query->whereIn('report_status', ['not-started', 'underway', 'in-review'])
                    ->whereIn('contracting_status', ['quoted', 'started']);
                break;
            case 'completed':
                // Completed tab mapping: report_status = 'completed'
                $query->where('report_status', 'completed');
                break;
            case 'closed':
                // Closed tab mapping: contracting_status = 'closed'
                $query->where('contracting_status', 'closed');
                break;
            case 'all':
            default:
                // No status filter - show all
                break;
        }
    }
}