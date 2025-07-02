<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Models\HB837;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InspectionCalendarController extends Controller
{
    /**
     * Display the inspection calendar
     */
    public function index(Request $request)
    {
        return view('admin.hb837.inspection-calendar.index');
    }

    /**
     * Get calendar events data (AJAX endpoint)
     */
    public function getEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $status = $request->get('status');

        $query = HB837::whereNotNull('scheduled_date_of_inspection');

        // Filter by date range
        if ($start && $end) {
            $query->whereBetween('scheduled_date_of_inspection', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        // Filter by status if provided
        if ($status && $status !== 'all') {
            $query->where('report_status', $status);
        }

        $projects = $query->with(['user', 'consultant'])->get();

        $events = [];
        foreach ($projects as $project) {
            $statusColors = $this->getStatusColors($project->report_status);

            $events[] = [
                'id' => $project->id,
                'title' => $project->property_name ?: 'Unnamed Property',
                'start' => $project->scheduled_date_of_inspection,
                'backgroundColor' => $statusColors['background'],
                'borderColor' => $statusColors['border'],
                'textColor' => $statusColors['text'],
                'extendedProps' => [
                    'address' => $project->address,
                    'city' => $project->city,
                    'state' => $project->state,
                    'zip' => $project->zip,
                    'status' => $project->report_status,
                    'consultant' => $project->consultant ? $project->consultant->name : 'Unassigned',
                    'quoted_price' => $project->quoted_price,
                    'units' => $project->units,
                    'notes' => $project->notes,
                ]
            ];
        }

        return response()->json($events);
    }

    /**
     * Update inspection date (AJAX endpoint)
     */
    public function updateInspectionDate(Request $request, $id)
    {
        $request->validate([
            'scheduled_date_of_inspection' => 'required|date',
        ]);

        try {
            $project = HB837::findOrFail($id);
            $project->update([
                'scheduled_date_of_inspection' => $request->scheduled_date_of_inspection
            ]);

            Log::info('Inspection date updated via calendar', [
                'project_id' => $id,
                'old_date' => $project->getOriginal('scheduled_date_of_inspection'),
                'new_date' => $request->scheduled_date_of_inspection,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inspection date updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update inspection date', [
                'project_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update inspection date: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get project details for modal (AJAX endpoint)
     */
    public function getProjectDetails($id)
    {
        try {
            $project = HB837::with(['user', 'assignedConsultant'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'project' => [
                    'id' => $project->id,
                    'property_name' => $project->property_name,
                    'address' => $project->address,
                    'city' => $project->city,
                    'state' => $project->state,
                    'zip' => $project->zip,
                    'scheduled_date_of_inspection' => $project->scheduled_date_of_inspection,
                    'report_status' => $project->report_status,
                    'contracting_status' => $project->contracting_status,
                    'quoted_price' => $project->quoted_price,
                    'units' => $project->units,
                    'notes' => $project->notes,
                    'consultant' => $project->assignedConsultant ? [
                        'id' => $project->assignedConsultant->id,
                        'name' => $project->assignedConsultant->name,
                        'email' => $project->assignedConsultant->email,
                    ] : null,
                    'created_by' => $project->user ? $project->user->name : 'Unknown',
                    'created_at' => $project->created_at->format('M d, Y'),
                    'updated_at' => $project->updated_at->format('M d, Y g:i A'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }
    }

    /**
     * Get status colors for calendar events
     */
    private function getStatusColors($status)
    {
        $colors = [
            'not-started' => [
                'background' => '#6f42c1',
                'border' => '#5a32a3',
                'text' => '#ffffff'
            ],
            'in-progress' => [
                'background' => '#fd7e14',
                'border' => '#e8590c',
                'text' => '#ffffff'
            ],
            'in-review' => [
                'background' => '#20c997',
                'border' => '#1ba085',
                'text' => '#ffffff'
            ],
            'completed' => [
                'background' => '#28a745',
                'border' => '#1e7e34',
                'text' => '#ffffff'
            ],
            'on-hold' => [
                'background' => '#dc3545',
                'border' => '#c82333',
                'text' => '#ffffff'
            ],
            'cancelled' => [
                'background' => '#6c757d',
                'border' => '#5a6268',
                'text' => '#ffffff'
            ],
            'pending' => [
                'background' => '#ffc107',
                'border' => '#e0a800',
                'text' => '#212529'
            ],
        ];

        return $colors[$status] ?? $colors['not-started'];
    }

    /**
     * Get available statuses for filter
     */
    public function getStatuses()
    {
        $statuses = HB837::whereNotNull('scheduled_date_of_inspection')
                         ->distinct()
                         ->pluck('report_status')
                         ->filter()
                         ->values();

        return response()->json($statuses);
    }
}
