<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Models\HB837;
use App\Models\Owner;
use App\Models\Client;
use App\Models\HB837File;
use App\Models\Consultant;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Exports\HB837Export;
use App\Imports\HB837Import;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class HB837Controller extends Controller
{


    public function index(Request $request, $tab = 'active')
    {
        $tab = in_array($tab = Str::lower($tab), ['active', 'quoted', 'completed', 'closed']) ? $tab : 'quoted';

        $sort = in_array($request->get('sort'), $this->sortableColumns(), true)
            ? $request->get('sort')
            : 'created_at';

        $direction = in_array(strtolower($request->get('direction')), ['asc', 'desc'], true)
            ? strtolower($request->get('direction'))
            : 'desc';

        $numRows = in_array((int) $request->get('num_rows'), [10, 25, 50, 100], true)
            ? (int) $request->get('num_rows')
            : 10;

        $query = HB837::query();
        $this->applyTabFilters($query, $tab);
        $this->applySearch($query, $request->get('search'));

        $projects = $query->orderByRaw("{$sort} IS NULL, {$sort} {$direction}")
            ->paginate($numRows);

        $backupPath = 'backup/hb837_projects.xlsx';
        $savedDate = Storage::exists($backupPath)
            ? date('Y-m-d H:i:s', Storage::lastModified($backupPath))
            : null;

        return view('admin.hb837.hb837', [
            'hb837' => $projects,
            'tab' => $tab,
            'savedDate' => $savedDate,
            'sort' => $sort,
            'direction' => $direction,
            'num_rows' => $numRows,
            'search' => $request->get('search')
        ]);
    }

    protected function sortableColumns()
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
            'report_submitted'
        ];
    }

    protected function applyTabFilters($query, $tab)
    {
        switch ($tab) {
            case 'active':
                $query->whereIn('report_status', ['not-started', 'in-progress', 'in-review'])
                    ->where('contracting_status', 'executed');
                break;
            case 'quoted':
                $query->whereIn('contracting_status', ['quoted', 'started']);
                break;
            case 'completed':
                $query->where('report_status', 'completed');
                break;
            case 'closed':
                $query->where('contracting_status', 'closed');
                break;
        }
    }

    protected function applySearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('property_name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('county', 'LIKE', "%{$search}%")
                    ->orWhere('macro_client', 'LIKE', "%{$search}%");
            });
        }
    }

    //#################################
    // Create and Store HB837 Project
    //#################################

    public function create()
    {
        return view('admin.hb837.create-hb837');
    }

    public function store(Request $request)
    {
        $request->merge([
            'quoted_price' => $this->normalizeCurrency($request->input('quoted_price')),
            'sub_fees_estimated_expenses' => $this->normalizeCurrency($request->input('sub_fees_estimated_expenses')),
        ]);

        $validated = $request->validate([
            'property_name' => 'nullable|string|max:255',
            'property_type' => 'nullable|in:garden,midrise,highrise,other',
            'units' => 'nullable|integer|min:0',
            'management_company' => 'nullable|string|max:255',

            'owner_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'county' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'zip' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:25',

            'assigned_consultant_id' => 'nullable|integer|exists:consultants,id',
            'scheduled_date_of_inspection' => 'nullable|date',
            'report_status' => 'nullable|in:not-started,in-progress,in-review,completed',
            'contracting_status' => 'nullable|in:quoted,started,executed,closed',

            'quoted_price' => 'nullable|numeric',
            'sub_fees_estimated_expenses' => 'nullable|numeric',

            'billing_req_sent' => 'nullable|date',
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
        ]);

        // Consultant select workaround
        if ($validated['assigned_consultant_id'] == -1) {
            $validated['assigned_consultant_id'] = null;
        }

        // Resolve or create owner ID
        if (!empty($validated['owner_name'])) {
            $validated['owner_id'] = Owner::firstOrCreate([
                'name' => trim($validated['owner_name'])
            ])->id;
        }

        // Calculate net profit
        $validated['project_net_profit'] = $validated['quoted_price'] && $validated['sub_fees_estimated_expenses']
            ? $validated['quoted_price'] - $validated['sub_fees_estimated_expenses']
            : null;

        $hb837 = HB837::create($validated);

        session()->flash('success', 'Record created successfully!');
        return redirect()->to("admin/hb837/{$hb837->id}/edit/general");
    }

    private function normalizeCurrency($value)
    {
        return $value !== '' ? floatval(preg_replace('/[^\d.]/', '', $value)) : null;
    }

    //#################################
    // Edit and Update HB837 Project
    //#################################

    /**
     * Show the edit form for a specific HB837 project.
     * @method GET
     *
     * @param int $id
     * @param string $tab
     * @return \Illuminate\View\View
     */
    public function edit($id, $tab = 'general')
    {
        $fields = [
            'general' => [
                'property_name',
                'property_type',
                'units',
                'management_company',
                'securitygauge_crime_risk',
                'assigned_consultant_id',
                'scheduled_date_of_inspection',
                'report_status',
                'contracting_status',
                'macro_client',
                'macro_contact',
                'macro_email',
            ],
            'address' => [
                'address',
                'city',
                'county',
                'state',
                'zip',
            ],
            'contacts' => [
                'owner_name',
                'property_manager_name',
                'property_manager_email',
                'regional_manager_name',
                'regional_manager_email',
                'phone',
            ],
            'financials' => [
                'quoted_price',
                'sub_fees_estimated_expenses',
                'project_net_profit',
                'billing_req_sent',
                'report_submitted',
                'agreement_submitted',
                'financial_notes',
            ],
            'files' => [
                'related_files',
            ],
            'notes' => [
                'notes',
            ],
        ];

        // Load record and relationships
        $hb837 = HB837::with('files')->findOrFail($id);

        // Optional: preload consultant list, clients, owners, etc.
        $clients = Client::all();
        $owners = Owner::all();
        $consultants = Consultant::all(['id', 'first_name', 'last_name']);

        // Ensure valid tab
        $tab = array_key_exists($tab, $fields) ? $tab : 'general';

        return view('admin.hb837.edit-hb837', [
            'hb837' => $hb837,
            'clients' => $clients,
            'owners' => $owners,
            'consultants' => $consultants,
            'tabFields' => $fields[$tab],
            'tab' => $tab
        ]);
    }


    /**
     * @param \Illuminate\Http\Request $request
     * @method POST
     * @param mixed $id
     * @param mixed $tab
     * @return RedirectResponse
     */
    public function update(Request $request, $id, $tab)
    {
        switch ($tab) {
            case 'general':
                return $this->updateGeneral($request, $id);
            case 'address':
                return $this->updateAddress($request, $id);
            case 'contacts':
                return $this->updateContacts($request, $id);
            case 'financials':
                return $this->updateFinancials($request, $id);
            case 'files':
                return $this->updateFiles($request, $id);
            case 'notes':
                return $this->updateNotes($request, $id);
            default:
                abort(404, 'Invalid tab specified.');
        }
    }


    protected function updateGeneral(Request $request, $id)
    {
        $fields = [
            'property_name',
            'property_type',
            'units',
            'management_company',
            'securitygauge_crime_risk',
            'assigned_consultant_id',
            'scheduled_date_of_inspection',
            'report_status',
            'report_submitted',
            'contracting_status',
            'macro_client',
            'macro_contact',
            'macro_email',
        ];

        $rules = [
            'property_name' => 'nullable|string|max:255',
            'property_type' => 'required|string|max:255',
            'units' => 'nullable|integer|min:0',
            'management_company' => 'nullable|string|max:255',
            'securitygauge_crime_risk' => 'nullable|string|max:255',
            'assigned_consultant_id' => 'nullable|integer',
            'scheduled_date_of_inspection' => 'nullable|date',
            'report_status' => 'required|in:not-started,in-progress,in-review,completed',
            'report_submitted' => 'nullable|date',
            'contracting_status' => 'required|in:quoted,started,executed,closed',
            'macro_client' => 'nullable|string|max:255',
            'macro_contact' => 'nullable|string|max:255',
            'macro_email' => 'nullable|email|max:255',
        ];

        $validatedData = $request->validate(Arr::only($rules, $fields));

        // if no assigned_consultant_id is provided, set it to null
        if ($validatedData['assigned_consultant_id'] == "-1") {
            $validatedData['assigned_consultant_id'] = null;
        }

        $hb837 = HB837::findOrFail($id);
        $hb837->update($validatedData);

        session()->flash('success', 'General section updated successfully!');
        return redirect()->to('admin/hb837/' . $hb837->id . '/edit/general');
    }


    protected function updateAddress(Request $request, $id)
    {
        $rules = [
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'zip' => 'required|string|max:10',
            'county' => 'nullable|string|max:255',
        ];

        $validatedData = $request->validate($rules);

        $hb837 = HB837::findOrFail($id);
        $hb837->update($validatedData);

        session()->flash('success', 'Address section updated successfully!');
        return redirect()->to("admin/hb837/{$hb837->id}/edit/address");
    }

    protected function updateContacts(Request $request, $id)
    {
        $rules = [
            'owner_name' => 'nullable|string|max:255',
            'property_manager_name' => 'nullable|string|max:255',
            'property_manager_email' => 'nullable|email|max:255',
            'regional_manager_name' => 'nullable|string|max:255',
            'regional_manager_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:25',
        ];

        $validatedData = $request->validate($rules);

        // If owner_name is set, assign or create owner_id
        if (!empty($validatedData['owner_name'])) {
            $owner = Owner::firstOrCreate(['name' => trim($validatedData['owner_name'])]);
            $validatedData['owner_id'] = $owner->id;
        }

        $hb837 = HB837::findOrFail($id);
        $hb837->update($validatedData);

        session()->flash('success', 'Contacts section updated successfully!');
        return redirect()->to('admin/hb837/' . $hb837->id . '/edit/contacts');
    }

    protected function updateFinancials(Request $request, $id)
    {
        $rules = [
            'quoted_price' => 'required|numeric|min:0',
            'sub_fees_estimated_expenses' => 'required|numeric|min:0',
            'project_net_profit' => 'nullable|numeric|min:0',
            'billing_req_sent' => 'nullable|date',
            'report_submitted' => 'nullable|date',
            'agreement_submitted' => 'nullable|date',
            'report_status' => 'nullable|in:not-started,in-progress,in-review,completed',
            'contracting_status' => 'nullable|in:quoted,started,executed,closed',
            'note_to_consultant' => 'nullable|string|max:1000',
            'financial_notes' => 'nullable|string|max:1000',
        ];

        // Normalize currency input
        $request->merge([
            'quoted_price' => $this->normalizeCurrency($request->input('quoted_price')),
            'sub_fees_estimated_expenses' => $this->normalizeCurrency($request->input('sub_fees_estimated_expenses')),
            'project_net_profit' => $this->normalizeCurrency($request->input('project_net_profit')),
        ]);

        $validatedData = $request->validate($rules);

        // Auto-calculate net profit if not manually entered
        if (is_null($validatedData['project_net_profit']) && $validatedData['quoted_price'] && $validatedData['sub_fees_estimated_expenses']) {
            $validatedData['project_net_profit'] = $validatedData['quoted_price'] - $validatedData['sub_fees_estimated_expenses'];
        }

        $hb837 = HB837::findOrFail($id);
        $hb837->update($validatedData);

        session()->flash('success', 'Financial section updated successfully!');
        return redirect()->to("admin/hb837/{$hb837->id}/edit/financials");
    }

    protected function updateFiles(Request $request, $id)
    {
        $hb837 = HB837::findOrFail($id);

        // Handle file uploads
        if ($request->hasFile('related_files')) {
            foreach ($request->file('related_files') as $file) {
                $path = $file->store('hb837_files'); // Saves to storage/app/public/hb837_files
                $hb837->files()->create([
                    'filename' => $file->getClientOriginalName(),
                    'original_filename' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'user_id' => auth()->id(),
                ]);
            }
        }

        // Handle file deletions
        if ($request->has('delete_files')) {
            $filesToDelete = $hb837->files()->whereIn('id', $request->input('delete_files'))->get();
            foreach ($filesToDelete as $file) {
                try {
                    if (Storage::exists($file->file_path)) {
                        Storage::delete($file->file_path);
                    }
                    // Always delete the reference, even if the file does not exist
                    $file->delete();

                } catch (\Exception $e) {
                    return back()->withErrors(['error' => 'Error deleting file: ' . $file->filename . ' - ' . $e->getMessage()]);
                }
            }
        }

        session()->flash('success', 'Files updated successfully!');
        return redirect()->to('admin/hb837/' . $hb837->id . '/edit/files');
    }


    public function deleteFile($id)
    {
        // Find the file record
        $file = HB837File::findOrFail($id);

        // Delete the file from storage
        if (File::exists(storage_path("app/{$file->file_path}"))) {
            try {
                File::delete(storage_path("app/{$file->file_path}"));
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Error deleting file: ' . $e->getMessage()]);
            }
        }

        // Delete the record from the database
        $file->delete();

        // Redirect back with success message
        return back()->with('success', 'File deleted successfully.');
    }


    protected function updateNotes(Request $request, $id)
    {
        // Validate both fields (don't limit to 'notes' only)
        $rules = [
            'notes' => 'nullable|string',
            'consultant_notes' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules); // Don't filter with Arr::only

        $hb837 = HB837::findOrFail($id);

        // Update fields if present
        $hb837->notes = $validatedData['notes'] ?? $hb837->notes;
        $hb837->consultant_notes = $validatedData['consultant_notes'] ?? $hb837->consultant_notes;

        $hb837->save();

        session()->flash('success', 'Notes updated successfully!');
        return redirect()->to("admin/hb837/{$hb837->id}/edit/notes");
    }

    public function report($id)
    {
        $hb837 = HB837::findOrFail($id);
        $html = view('admin.hb837.report', compact('hb837'))->render();
        $pdf = Pdf::loadHTML($html);
        return $pdf->stream('report.pdf');
    }
}

