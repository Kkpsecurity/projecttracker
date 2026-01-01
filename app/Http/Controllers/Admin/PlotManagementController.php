<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use App\Models\PlotGroup;
use App\Models\PlotAddress;
use App\Models\HB837;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PlotManagementController extends Controller
{
    /**
     * Display the unified plot management dashboard
     */
    public function index()
    {
        // Get macro clients with their plots
        $macroClients = HB837::whereNotNull('macro_client')
            ->where('macro_client', '!=', '')
            ->select('macro_client')
            ->distinct()
            ->with(['plots.address', 'plots.plotGroups'])
            ->get()
            ->groupBy('macro_client')
            ->map(function ($projects, $clientName) {
                $allPlots = $projects->flatMap->plots->unique('id');
                return [
                    'name' => $clientName,
                    'plots_count' => $allPlots->count(),
                    'projects_count' => $projects->count(),
                    'plots' => $allPlots, // Remove .load() since relationships are already loaded
                ];
            });

        // Get unassigned plot groups (plots not assigned to any HB837 project)
        $unassignedGroups = PlotGroup::with([
            'plots' => function ($query) {
                $query->whereNull('hb837_id');
            },
            'plots.address'
        ])
            ->where('is_active', true)
            ->get()
            ->filter(function ($group) {
                return $group->plots->count() > 0;
            });

        // Get completely unassigned plots (not in any group and not assigned to HB837)
        $orphanedPlots = Plot::whereNull('hb837_id')
            ->whereDoesntHave('plotGroups')
            ->with(['address'])
            ->get();

        // Statistics
        $stats = [
            'total_plots' => Plot::count(),
            'assigned_plots' => Plot::whereNotNull('hb837_id')->count(),
            'grouped_plots' => Plot::whereHas('plotGroups')->count(),
            'orphaned_plots' => $orphanedPlots->count(),
            'macro_clients' => $macroClients->count(),
            'unassigned_groups' => $unassignedGroups->count(),
        ];

        return view('admin.plots.management', compact(
            'macroClients',
            'unassignedGroups',
            'orphanedPlots',
            'stats'
        ));
    }

    /**
     * Assign plots to a macro client (create or update HB837 project)
     */
    public function assignToClient(Request $request)
    {
        $request->validate([
            'plot_ids' => 'required|array',
            'plot_ids.*' => 'exists:plots,id',
            'macro_client' => 'required|string|max:255',
            'property_name' => 'required|string|max:255',
            'property_type' => 'nullable|string',
            'create_new_project' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $plots = Plot::whereIn('id', $request->plot_ids)->with('address')->get();

            if ($request->create_new_project) {
                // Create new HB837 project for each plot
                foreach ($plots as $plot) {
                    $hb837 = HB837::create([
                        'macro_client' => $request->macro_client,
                        'property_name' => $request->property_name . ' - ' . ($plot->address->address_line_1 ?? 'Plot ' . $plot->id),
                        'property_type' => $request->property_type ?? 'garden',
                        'address' => $plot->address->address_line_1 ?? '',
                        'city' => $plot->address->city ?? '',
                        'state' => $plot->address->state ?? '',
                        'zip' => $plot->address->zip_code ?? '',
                        'report_status' => 'not-started',
                        'contracting_status' => 'quoted',
                        'user_id' => Auth::id(),
                    ]);

                    $plot->update(['hb837_id' => $hb837->id]);
                }
            } else {
                // Find existing project for this macro client or create one
                $hb837 = HB837::where('macro_client', $request->macro_client)
                    ->where('property_name', $request->property_name)
                    ->first();

                if (!$hb837) {
                    $hb837 = HB837::create([
                        'macro_client' => $request->macro_client,
                        'property_name' => $request->property_name,
                        'property_type' => $request->property_type ?? 'garden',
                        'report_status' => 'not-started',
                        'contracting_status' => 'quoted',
                        'user_id' => Auth::id(),
                    ]);
                }

                // Assign all plots to this project
                Plot::whereIn('id', $request->plot_ids)->update(['hb837_id' => $hb837->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plots successfully assigned to ' . $request->macro_client,
                'assigned_count' => count($request->plot_ids),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error assigning plots: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Move plots between groups or clients
     */
    public function movePlots(Request $request)
    {
        $request->validate([
            'plot_ids' => 'required|array',
            'plot_ids.*' => 'exists:plots,id',
            'action' => 'required|in:assign_to_client,move_to_group,remove_assignment',
            'target_macro_client' => 'nullable|string',
            'target_group_id' => 'nullable|exists:plot_groups,id',
        ]);

        try {
            DB::beginTransaction();

            switch ($request->action) {
                case 'assign_to_client':
                    // Find or create HB837 project for macro client
                    $plots = Plot::whereIn('id', $request->plot_ids)->with('address')->get();

                    foreach ($plots as $plot) {
                        $hb837 = HB837::where('macro_client', $request->target_macro_client)
                            ->first();

                        if (!$hb837) {
                            $hb837 = HB837::create([
                                'macro_client' => $request->target_macro_client,
                                'property_name' => 'Property for ' . $request->target_macro_client,
                                'report_status' => 'not-started',
                                'contracting_status' => 'quoted',
                                'user_id' => Auth::id(),
                            ]);
                        }

                        $plot->update(['hb837_id' => $hb837->id]);
                        // Remove from plot groups when assigned to client
                        $plot->plotGroups()->detach();
                    }
                    break;

                case 'move_to_group':
                    Plot::whereIn('id', $request->plot_ids)->update(['hb837_id' => null]);
                    $plotGroup = PlotGroup::findOrFail($request->target_group_id);

                    foreach ($request->plot_ids as $plotId) {
                        $plotGroup->plots()->syncWithoutDetaching([
                            $plotId => [
                                'sort_order' => $plotGroup->plots()->count() + 1,
                                'notes' => 'Moved via management interface',
                            ]
                        ]);
                    }
                    break;

                case 'remove_assignment':
                    Plot::whereIn('id', $request->plot_ids)->update(['hb837_id' => null]);
                    Plot::whereIn('id', $request->plot_ids)->each(function ($plot) {
                        $plot->plotGroups()->detach();
                    });
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plots moved successfully',
                'moved_count' => count($request->plot_ids),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error moving plots: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new plot group
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'plot_ids' => 'nullable|array',
            'plot_ids.*' => 'exists:plots,id',
        ]);

        try {
            DB::beginTransaction();

            $plotGroup = PlotGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'color' => $request->color ?? '#007bff',
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);

            if ($request->plot_ids) {
                foreach ($request->plot_ids as $index => $plotId) {
                    $plotGroup->plots()->attach($plotId, [
                        'sort_order' => $index + 1,
                        'notes' => 'Added during group creation',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plot group created successfully',
                'group' => $plotGroup->load('plots'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating group: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get data for AJAX updates
     */
    public function getData(Request $request)
    {
        $type = $request->get('type', 'all');

        switch ($type) {
            case 'macro_clients':
                return $this->getMacroClientsData();
            case 'unassigned_groups':
                return $this->getUnassignedGroupsData();
            case 'orphaned_plots':
                return $this->getOrphanedPlotsData();
            default:
                return $this->getAllData();
        }
    }

    private function getMacroClientsData()
    {
        $macroClients = HB837::whereNotNull('macro_client')
            ->where('macro_client', '!=', '')
            ->select('macro_client')
            ->distinct()
            ->with(['plots.address'])
            ->get()
            ->groupBy('macro_client');

        return response()->json($macroClients);
    }

    private function getUnassignedGroupsData()
    {
        // Get all unassigned plots (not linked to any HB837 project)
        $plots = Plot::whereNull('hb837_id')
            ->with(['address', 'plotGroups'])
            ->get();

        // Format for DataTables
        $formattedPlots = $plots->map(function ($plot) {
            return [
                'id' => $plot->id,
                'plot_name' => $plot->plot_name ?: "Plot {$plot->id}",
                'address' => $plot->address ? $plot->address->street_address : 'No address',
                'group_name' => $plot->plotGroups->first() ? $plot->plotGroups->first()->name : null,
                'group_color' => $plot->plotGroups->first() ? $plot->plotGroups->first()->color : null,
                'status' => $plot->plotGroups->count() > 0 ? 'Grouped' : 'Unassigned',
                'coordinates_latitude' => $plot->coordinates_latitude,
                'coordinates_longitude' => $plot->coordinates_longitude,
                'checkbox' => '<input type="checkbox" class="plot-checkbox" value="' . $plot->id . '">',
                'actions' => '<button class="btn btn-sm btn-info view-plot-btn" data-id="' . $plot->id . '">
                               <i class="fas fa-eye"></i> View
                             </button>'
            ];
        });

        return response()->json([
            'data' => $formattedPlots,
            'recordsTotal' => $formattedPlots->count(),
            'recordsFiltered' => $formattedPlots->count()
        ]);
    }

    private function getOrphanedPlotsData()
    {
        $plots = Plot::whereNull('hb837_id')
            ->whereDoesntHave('plotGroups')
            ->with(['address'])
            ->get();

        return response()->json($plots);
    }

    private function getAllData()
    {
        return response()->json([
            'macro_clients' => $this->getMacroClientsData()->getData(),
            'unassigned_groups' => $this->getUnassignedGroupsData()->getData(),
            'orphaned_plots' => $this->getOrphanedPlotsData()->getData(),
            'stats' => [
                'total_plots' => Plot::count(),
                'assigned_plots' => Plot::whereNotNull('hb837_id')->count(),
                'grouped_plots' => Plot::whereHas('plotGroups')->count(),
                'orphaned_plots' => Plot::whereNull('hb837_id')->whereDoesntHave('plotGroups')->count(),
            ],
        ]);
    }

    /**
     * Display Macro Clients management page
     */
    public function clients()
    {
        // Get macro clients with their plots and statistics
        $macroClients = HB837::whereNotNull('macro_client')
            ->where('macro_client', '!=', '')
            ->select('macro_client')
            ->distinct()
            ->get()
            ->groupBy('macro_client')
            ->map(function ($projects, $clientName) {
                $allProjects = HB837::where('macro_client', $clientName)->with(['plots.address'])->get();
                $allPlots = $allProjects->flatMap->plots->unique('id');

                return [
                    'name' => $clientName,
                    'plots_count' => $allPlots->count(),
                    'projects_count' => $allProjects->count(),
                    'plots' => $allPlots, // Remove the .load() call since it's already a Collection
                    'projects' => $allProjects,
                    'total_value' => $allProjects->sum('quoted_price') ?? 0,
                    'active_projects' => $allProjects->whereIn('report_status', ['in-progress', 'underway'])->count(),
                ];
            });

        // Statistics
        $stats = [
            'total_macro_clients' => $macroClients->count(),
            'total_assigned_plots' => Plot::whereNotNull('hb837_id')->count(),
            'active_projects' => HB837::whereIn('report_status', ['in-progress', 'underway'])->count(),
            'total_project_value' => HB837::sum('quoted_price') ?? 0,
        ];

        return view('admin.plots.clients', compact('macroClients', 'stats'));
    }

    /**
     * Display specific Macro Client details
     */
    public function showClient(string $clientName)
    {
        $client = HB837::where('macro_client', $clientName)
            ->with(['plots.address'])
            ->get();

        if ($client->isEmpty()) {
            abort(404, 'Macro Client not found');
        }

        $plots = $client->flatMap->plots->unique('id');
        $stats = [
            'plots_count' => $plots->count(),
            'projects_count' => $client->count(),
            'total_value' => $client->sum('quoted_price') ?? 0,
            'completed_projects' => $client->where('report_status', 'completed')->count(),
        ];

        return view('admin.plots.client-detail', compact('client', 'plots', 'stats', 'clientName'));
    }

    /**
     * Display Group Plots management page
     */
    public function groups()
    {
        // Get unassigned plot groups (plots not assigned to any HB837 project)
        $unassignedGroups = PlotGroup::with([
            'plots' => function ($query) {
                $query->whereNull('hb837_id');
            },
            'plots.address'
        ])
            ->where('is_active', true)
            ->get()
            ->filter(function ($group) {
                return $group->plots->count() > 0;
            });

        // Get completely unassigned plots (not in any group and not assigned to HB837)
        $orphanedPlots = Plot::whereNull('hb837_id')
            ->whereDoesntHave('plotGroups')
            ->with(['address'])
            ->get();

        // Get available macro clients for assignment
        $availableClients = HB837::whereNotNull('macro_client')
            ->where('macro_client', '!=', '')
            ->distinct()
            ->pluck('macro_client')
            ->sort()
            ->values();

        // Statistics
        $stats = [
            'total_groups' => PlotGroup::where('is_active', true)->count(),
            'total_unassigned_plots' => Plot::whereNull('hb837_id')->count(),
            'orphaned_plots' => $orphanedPlots->count(),
            'ready_for_grouping' => Plot::whereNull('hb837_id')->count(),
        ];

        // Get all plot groups (not just ones with unassigned plots for filter dropdown)
        $plotGroups = PlotGroup::where('is_active', true)
            ->withCount([
                'plots' => function ($query) {
                    $query->whereNull('hb837_id');
                }
            ])
            ->orderBy('name')
            ->get();

        // Debug: Log the groups count
        \Log::info('Plot groups loaded: ' . $plotGroups->count());
        \Log::info('Plot groups: ' . $plotGroups->pluck('name')->implode(', '));

        return view('admin.plots.groups-new', compact('unassignedGroups', 'orphanedPlots', 'availableClients', 'stats', 'plotGroups'));
    }

    /**
     * Assign plots from groups to a macro client
     */
    public function assignGroupsToClient(Request $request)
    {
        $request->validate([
            'plot_ids' => 'required|array',
            'plot_ids.*' => 'exists:plots,id',
            'macro_client' => 'required|string|max:255',
            'property_name' => 'required|string|max:255',
            'create_new_project' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $plots = Plot::whereIn('id', $request->plot_ids)
                ->whereNull('hb837_id') // Ensure they're unassigned
                ->with('address')
                ->get();

            if ($plots->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No unassigned plots found to assign.']);
            }

            // Create or find HB837 project
            $hb837 = HB837::firstOrCreate([
                'macro_client' => $request->macro_client,
                'property_name' => $request->property_name,
            ], [
                'address' => $plots->first()->address->street_address ?? '',
                'city' => $plots->first()->address->city ?? '',
                'state' => $plots->first()->address->state ?? '',
                'zip' => $plots->first()->address->zip_code ?? '',
                'report_status' => 'not-started',
                'created_by' => Auth::id(),
            ]);

            // Assign plots to the project
            $plots->each(function ($plot) use ($hb837) {
                $plot->update(['hb837_id' => $hb837->id]);
            });

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->plot_ids) . ' plots assigned to ' . $request->macro_client,
                'assigned_count' => $plots->count(),
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error assigning plots: ' . $e->getMessage()]);
        }
    }

    /**
     * Get all clients for AJAX dropdown
     */
    public function getClients()
    {
        try {
            $clients = HB837::whereNotNull('macro_client')
                ->where('macro_client', '!=', '')
                ->select('macro_client')
                ->distinct()
                ->orderBy('macro_client')
                ->get()
                ->map(function ($client) {
                    return [
                        'macro_client' => $client->macro_client,
                        'plots_count' => $client->plots()->count(),
                    ];
                });

            return response()->json([
                'success' => true,
                'clients' => $clients
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading clients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client details for modal display
     */
    public function getClientDetails(Request $request)
    {
        try {
            $clientName = $request->get('client');
            if (!$clientName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client name is required'
                ], 400);
            }

            $projects = HB837::where('macro_client', $clientName)->get();
            $totalPlots = $projects->sum(function ($project) {
                return $project->plots()->count();
            });

            $totalValue = $projects->sum(function ($project) {
                return floatval($project->property_value ?? 0);
            });

            $clientData = [
                'macro_client' => $clientName,
                'plots_count' => $totalPlots,
                'projects_count' => $projects->count(),
                'total_value' => $totalValue,
                'active_projects' => $projects->where('status', 'active')->count(),
                'projects' => $projects->map(function ($project) {
                    return [
                        'property_name' => $project->property_name,
                        'status' => $project->status ?? 'pending',
                        'value' => $project->property_value,
                        'plots_count' => $project->plots()->count(),
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'client' => $clientData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading client details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export client data (placeholder)
     */
    public function exportClientData(Request $request)
    {
        // Implementation for data export
        return response()->json([
            'success' => true,
            'message' => 'Export functionality will be implemented',
            'url' => '#'
        ]);
    }

    /**
     * Generate client report (placeholder)
     */
    public function generateClientReport(Request $request)
    {
        // Implementation for report generation
        return response()->json([
            'success' => true,
            'message' => 'Report generation functionality will be implemented',
            'url' => '#'
        ]);
    }

    /**
     * Get all groups for AJAX dropdown
     */
    public function getGroups()
    {
        try {
            $groups = PlotGroup::where('is_active', true)
                ->withCount([
                    'plots' => function ($query) {
                        $query->whereNull('hb837_id'); // Only unassigned plots
                    }
                ])
                ->orderBy('name')
                ->get()
                ->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'plots_count' => $group->plots_count,
                        'color' => $group->color ?? '#3498db',
                    ];
                });

            return response()->json([
                'success' => true,
                'groups' => $groups
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading groups: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Create a new macro client from selected plots
     */
    public function createClientFromPlots(Request $request)
    {
        try {
            $request->validate([
                'client_name' => 'required|string|max:255',
                'project_name' => 'required|string|max:255',
                'plot_ids' => 'required|array|min:1',
                'plot_ids.*' => 'exists:plots,id'
            ]);

            DB::beginTransaction();

            // Create HB837 project
            $project = HB837::create([
                'macro_client' => $request->client_name,
                'property_name' => $request->project_name,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // Assign plots to the project
            Plot::whereIn('id', $request->plot_ids)
                ->update(['hb837_id' => $project->id]);

            // Remove plots from any groups (they're now assigned)
            Plot::whereIn('id', $request->plot_ids)
                ->each(function ($plot) {
                    $plot->plotGroups()->detach();
                });

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Macro client created and plots assigned successfully!',
                'client' => [
                    'macro_client' => $project->macro_client,
                    'project_name' => $project->property_name,
                    'plots_count' => count($request->plot_ids)
                ]
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating client: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get clients data for DataTables
     */
    public function clientsData(Request $request)
    {
        return $this->getMacroClientsData();
    }

    /**
     * Get groups data for DataTables
     */
    public function groupsData(Request $request)
    {
        $groupFilter = $request->get('group_filter');

        // Base query for unassigned plots (not linked to any HB837 project)
        $query = Plot::whereNull('hb837_id')
            ->with(['address', 'plotGroups']);

        // Apply group filter if specified
        if ($groupFilter === 'unassigned') {
            // Show only plots that are not in any group
            $query->whereDoesntHave('plotGroups');
        } elseif ($groupFilter && is_numeric($groupFilter)) {
            // Show only plots that belong to the specified group
            $query->whereHas('plotGroups', function ($q) use ($groupFilter) {
                $q->where('plot_groups.id', $groupFilter);
            });
        }
        // If no filter or "All Groups", show all unassigned plots

        $plots = $query->get();

        // Format for DataTables
        $formattedPlots = $plots->map(function ($plot) {
            return [
                'id' => $plot->id,
                'plot_name' => $plot->plot_name ?: "Plot {$plot->id}",
                'address' => $plot->address ? $plot->address->street_address : 'No address',
                'group_name' => $plot->plotGroups->first() ? $plot->plotGroups->first()->name : null,
                'group_color' => $plot->plotGroups->first() ? $plot->plotGroups->first()->color : null,
                'status' => $plot->plotGroups->count() > 0 ? 'Grouped' : 'Unassigned',
                'coordinates_latitude' => $plot->coordinates_latitude,
                'coordinates_longitude' => $plot->coordinates_longitude,
                'checkbox' => '<input type="checkbox" class="plot-checkbox" value="' . $plot->id . '">',
                'actions' => '<button class="btn btn-sm btn-info view-plot-btn" data-id="' . $plot->id . '">
                               <i class="fas fa-eye"></i> View
                             </button>'
            ];
        });

        return response()->json([
            'data' => $formattedPlots,
            'recordsTotal' => $formattedPlots->count(),
            'recordsFiltered' => $formattedPlots->count()
        ]);
    }

    /**
     * Create a new plot with address and assign to a group
     */
    public function createPlotAndAssign(Request $request)
    {
        $request->validate([
            'group_id' => 'nullable|exists:plot_groups,id', // Make group_id optional
            'plot_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:50',
            'coordinates_latitude' => 'nullable|numeric|between:-90,90',
            'coordinates_longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string',
            'lot_number' => 'nullable|string|max:50',
            'block_number' => 'nullable|string|max:50',
            'subdivision_name' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Create the plot
            $plot = Plot::create([
                'plot_name' => $request->plot_name,
                'description' => $request->description,
                'lot_number' => $request->lot_number,
                'block_number' => $request->block_number,
                'subdivision_name' => $request->subdivision_name,
                'coordinates_latitude' => $request->coordinates_latitude,
                'coordinates_longitude' => $request->coordinates_longitude,
            ]);

            // Create the address
            PlotAddress::create([
                'plot_id' => $plot->id,
                'street_address' => $request->street_address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country ?? 'United States',
            ]);

            // Assign to the group if group_id is provided
            if ($request->group_id) {
                $plotGroup = PlotGroup::findOrFail($request->group_id);
                $nextSortOrder = $plotGroup->plots()->max('plot_group_plots.sort_order') + 1;

                $plotGroup->plots()->attach($plot->id, [
                    'sort_order' => $nextSortOrder,
                    'notes' => 'Added via new plot creation',
                ]);
            }

            DB::commit();

            // Load the plot with its relationships for the response
            $plot->load(['address', 'plotGroups']);

            return response()->json([
                'success' => true,
                'message' => 'Plot created successfully' . ($request->group_id ? ' and assigned to group' : ''),
                'plot' => $plot,
                'group' => $request->group_id ? $plotGroup : null,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating plot: ' . $e->getMessage(),
            ], 500);
        }
    }
}
