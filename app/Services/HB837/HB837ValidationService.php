<?php

namespace App\Services\HB837;

use Illuminate\Http\Request;

class HB837ValidationService
{
    /**
     * Get validation rules for HB837 creation
     */
    public function getCreateRules(): array
    {
        return [
            'property_name' => 'required|string|max:255',
            'management_company' => 'nullable|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'property_type' => 'nullable|string|max:255',
            'units' => 'nullable|integer|min:1',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'zip' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'assigned_consultant_id' => 'nullable|integer|exists:consultants,id',
            'scheduled_date_of_inspection' => 'nullable|date',
            'report_status' => 'nullable|in:not-started,underway,in-review,completed',
            'contracting_status' => 'nullable|in:quoted,started,executed,closed',
            'quoted_price' => 'nullable|numeric',
            'sub_fees_estimated_expenses' => 'nullable|numeric',
            'billing_req_sent' => 'nullable|date',
            'billing_req_submitted' => 'nullable|date',
            'report_submitted' => 'nullable|date',
            'agreement_submitted' => 'nullable|date',
            'project_net_profit' => 'nullable|numeric',
            'securitygauge_crime_risk' => 'nullable|string|max:255',
            'macro_client' => 'nullable|string|max:255',
            'macro_contact' => 'nullable|string|max:255',
            'macro_email' => 'nullable|email|max:255',
            'property_manager_name' => 'nullable|string|max:255',
            'property_manager_email' => 'nullable|email|max:255',
            'regional_manager_name' => 'nullable|string|max:255',
            'regional_manager_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'consultant_notes' => 'nullable|string'
        ];
    }

    /**
     * Get validation rules for HB837 update
     */
    public function getUpdateRules(): array
    {
        $rules = $this->getCreateRules();
        $rules['financial_notes'] = 'nullable|string';
        return $rules;
    }

    /**
     * Process validated data for creation/update
     */
    public function processValidatedData(array $validated): array
    {
        // Set user_id to current authenticated user for creation
        if (!isset($validated['user_id'])) {
            $validated['user_id'] = \Illuminate\Support\Facades\Auth::id();
        }

        // Consultant select workaround
        if (($validated['assigned_consultant_id'] ?? null) == -1) {
            $validated['assigned_consultant_id'] = null;
        }

        // Calculate net profit
        if (isset($validated['quoted_price']) && isset($validated['sub_fees_estimated_expenses'])) {
            $validated['project_net_profit'] = $validated['quoted_price'] - $validated['sub_fees_estimated_expenses'];
        }

        return $validated;
    }

    /**
     * Validate status update request
     */
    public function validateStatusUpdate(Request $request): void
    {
        $request->validate([
            'status' => 'required|in:not-started,underway,in-review,completed'
        ]);
    }

    /**
     * Validate priority update request
     */
    public function validatePriorityUpdate(Request $request): void
    {
        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent'
        ]);
    }

    /**
     * Validate export request
     */
    public function validateExportRequest(Request $request): void
    {
        $request->validate([
            'format' => 'nullable|in:excel,csv,pdf',
            'tab' => 'nullable|in:all,active,quoted,completed,closed',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'statuses' => 'nullable|array',
            'statuses.*' => 'in:not-started,underway,in-review,completed',
            'include_consultant' => 'nullable|boolean'
        ]);
    }

    /**
     * Validate import request
     */
    public function validateImportRequest(Request $request): void
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'import_phase' => 'nullable|in:initial,update,review',
            'action' => 'nullable|in:preview,import'
        ]);
    }

    /**
     * Validate search request
     */
    public function validateSearchRequest(Request $request): void
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);
    }

    /**
     * Get allowed sortable columns
     */
    public function getSortableColumns(): array
    {
        return [
            'created_at',
            'updated_at',
            'property_name',
            'county',
            'macro_client',
            'assigned_consultant_id',
            'scheduled_date_of_inspection',
            'report_status',
            'property_type',
            'units',
            'management_company',
            'agreement_submitted',
            'contracting_status',
            'billing_req_sent',
            'billing_req_submitted',
            'report_submitted',
            'securitygauge_crime_risk'
        ];
    }

    /**
     * Get allowed tab values
     */
    public function getAllowedTabs(): array
    {
        return ['all', 'active', 'quoted', 'completed', 'closed'];
    }

    /**
     * Validate and sanitize tab parameter
     */
    public function validateTab(string $tab): string
    {
        $allowedTabs = $this->getAllowedTabs();
        return in_array(strtolower($tab), $allowedTabs) ? strtolower($tab) : 'active';
    }
}
