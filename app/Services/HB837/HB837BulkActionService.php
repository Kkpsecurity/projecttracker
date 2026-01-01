<?php

namespace App\Services\HB837;

use App\Models\HB837;
use App\Models\Consultant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HB837BulkActionService
{
    /**
     * Perform bulk actions on multiple HB837 records
     */
    public function executeBulkAction(Request $request): array
    {
        $this->validateBulkAction($request);

        $records = HB837::whereIn('id', $request->selected_ids);
        $count = $records->count();

        switch ($request->action) {
            case 'delete':
                return $this->bulkDelete($records, $count);

            case 'status_update':
                return $this->bulkStatusUpdate($records, $count, $request->bulk_status);

            case 'consultant_assign':
                return $this->bulkConsultantAssign($records, $count, $request->bulk_consultant_id);

            case 'priority_update':
                return $this->bulkPriorityUpdate($records, $count, $request->bulk_priority);

            default:
                return ['success' => false, 'message' => 'Invalid action.'];
        }
    }

    /**
     * Validate bulk action request
     */
    private function validateBulkAction(Request $request): void
    {
        $request->validate([
            'action' => 'required|in:delete,status_update,consultant_assign,priority_update',
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'integer|exists:hb837,id',
            'bulk_status' => 'nullable|in:not-started,underway,in-review,completed',
            'bulk_consultant_id' => 'nullable|integer|exists:consultants,id',
            'bulk_priority' => 'nullable|in:low,normal,high,urgent'
        ]);
    }

    /**
     * Execute bulk delete
     */
    private function bulkDelete($records, int $count): array
    {
        try {
            $records->delete();
            return [
                'success' => true,
                'message' => "{$count} records deleted successfully."
            ];
        } catch (\Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Bulk delete failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Execute bulk status update
     */
    private function bulkStatusUpdate($records, int $count, string $status): array
    {
        try {
            $records->update(['report_status' => $status]);
            $statusDisplay = ucfirst(str_replace('-', ' ', $status));
            return [
                'success' => true,
                'message' => "{$count} records updated to {$statusDisplay} status."
            ];
        } catch (\Exception $e) {
            Log::error('Bulk status update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Bulk status update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Execute bulk consultant assignment
     */
    private function bulkConsultantAssign($records, int $count, ?int $consultantId): array
    {
        try {
            $records->update(['assigned_consultant_id' => $consultantId]);
            
            $consultantName = 'Unassigned';
            if ($consultantId) {
                $consultant = Consultant::find($consultantId);
                if ($consultant) {
                    $consultantName = $consultant->first_name . ' ' . $consultant->last_name;
                }
            }

            return [
                'success' => true,
                'message' => "{$count} records assigned to {$consultantName}."
            ];
        } catch (\Exception $e) {
            Log::error('Bulk consultant assignment error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Bulk consultant assignment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Execute bulk priority update
     */
    private function bulkPriorityUpdate($records, int $count, string $priority): array
    {
        try {
            $records->update(['priority' => $priority]);
            $priorityDisplay = ucfirst($priority);
            return [
                'success' => true,
                'message' => "{$count} records updated to {$priorityDisplay} priority."
            ];
        } catch (\Exception $e) {
            Log::error('Bulk priority update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Bulk priority update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Quick status update for a single record
     */
    public function updateStatus(HB837 $hb837, string $status): array
    {
        try {
            $hb837->update(['report_status' => $status]);
            $statusDisplay = ucfirst(str_replace('-', ' ', $status));
            
            return [
                'success' => true,
                'message' => "Status updated to {$statusDisplay}."
            ];
        } catch (\Exception $e) {
            Log::error('Status update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Status update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Quick priority update for a single record
     */
    public function updatePriority(HB837 $hb837, string $priority): array
    {
        try {
            $hb837->update(['priority' => $priority]);
            $priorityDisplay = ucfirst($priority);
            
            return [
                'success' => true,
                'message' => "Priority updated to {$priorityDisplay}."
            ];
        } catch (\Exception $e) {
            Log::error('Priority update error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Priority update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Duplicate an existing HB837 record
     */
    public function duplicate(HB837 $hb837): array
    {
        try {
            $data = $hb837->toArray();

            // Remove fields that shouldn't be duplicated
            unset($data['id'], $data['created_at'], $data['updated_at']);

            // Modify the property name to indicate it's a duplicate
            $data['property_name'] = $data['property_name'] . ' (Copy)';

            // Reset status fields for the duplicate
            $data['report_status'] = 'not-started';
            $data['scheduled_date_of_inspection'] = null;
            $data['billing_req_sent'] = null;
            $data['report_submitted'] = null;
            $data['agreement_submitted'] = null;

            $duplicate = HB837::create($data);

            return [
                'success' => true,
                'message' => 'Record duplicated successfully!',
                'duplicate_id' => $duplicate->id
            ];

        } catch (\Exception $e) {
            Log::error('HB837 Duplicate Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Duplication failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle bulk actions from controller
     */
    public function handleBulkAction(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        try {
            switch ($action) {
                case 'delete':
                    return $this->deleteBulk($ids);
                    
                case 'update_status':
                    return $this->updateStatusBulk($ids, $request->input('status'));
                    
                case 'update_priority':
                    return $this->updatePriorityBulk($ids, $request->input('priority'));
                    
                default:
                    return response()->json(['success' => false, 'message' => 'Unknown action'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Bulk action failed'], 500);
        }
    }

    /**
     * Update single record (unified method for status/priority updates)
     */
    public function updateSingleRecord(HB837 $hb837, array $data): \Illuminate\Http\JsonResponse
    {
        try {
            $hb837->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully',
                'record' => $hb837->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Single record update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete multiple records
     */
    private function deleteBulk(array $ids): \Illuminate\Http\JsonResponse
    {
        try {
            $count = HB837::whereIn('id', $ids)->delete();
            
            return response()->json([
                'success' => true,
                'message' => "{$count} records deleted successfully"
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk delete failed'
            ], 500);
        }
    }

    /**
     * Update status for multiple records
     */
    private function updateStatusBulk(array $ids, string $status): \Illuminate\Http\JsonResponse
    {
        try {
            $count = HB837::whereIn('id', $ids)->update(['report_status' => $status]);
            $statusDisplay = ucfirst(str_replace('-', ' ', $status));
            
            return response()->json([
                'success' => true,
                'message' => "{$count} records updated to {$statusDisplay}"
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk status update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk status update failed'
            ], 500);
        }
    }

    /**
     * Update priority for multiple records
     */
    private function updatePriorityBulk(array $ids, string $priority): \Illuminate\Http\JsonResponse
    {
        try {
            $count = HB837::whereIn('id', $ids)->update(['priority' => $priority]);
            $priorityDisplay = ucfirst($priority);
            
            return response()->json([
                'success' => true,
                'message' => "{$count} records updated to {$priorityDisplay} priority"
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk priority update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk priority update failed'
            ], 500);
        }
    }
}
