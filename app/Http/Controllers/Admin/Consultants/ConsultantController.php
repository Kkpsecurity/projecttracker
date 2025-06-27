<?php

namespace App\Http\Controllers\Admin\Consultants;

use App\Exports\ConsultantExport;
use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\HB837;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ConsultantController extends Controller
{
    /**
     * Display a paginated list of consultants.
     */
    public function index()
    {
        $consultants = Consultant::paginate(10);

        return view('admin.consultants.show', compact('consultants'));
    }

    /**
     * Show the form for creating a new consultant.
     */
    public function create()
    {
        return view('admin.consultants.create');
    }

    /**
     * Store a newly created consultant.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateConsultant($request);

        Consultant::create($validatedData);

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant created successfully.');
    }

    /**
     * Show the form for editing the specified consultant.
     */
    public function edit($id)
    {
        $consultant = Consultant::findOrFail($id);

        return view('admin.consultants.edit', compact('consultant'));
    }

    /**
     * Update the specified consultant.
     */
    public function update(Request $request, $id)
    {
        $consultant = Consultant::findOrFail($id);

        $validatedData = $this->validateConsultant($request, $consultant->id);

        // dd($validatedData);
        $consultant->update($validatedData);

        // Handle file uploads
        if ($request->hasFile('files')) {
            $this->uploadFiles($request, $consultant);
        }

        // Handle file deletions
        if ($request->has('delete_files')) {
            $this->deleteFiles($request->input('delete_files'), $consultant);
        }

        return redirect()->route('admin.consultants.edit', $consultant->id)
            ->with('success', 'Consultant updated successfully.');
    }

    /**
     * Remove the specified consultant.
     */
    public function destroy(Consultant $consultant)
    {
        $consultant->delete();

        return redirect()->route('admin.consultants.index')
            ->with('success', 'Consultant deleted successfully.');
    }

    /**
     * Fetch consultant details via AJAX.
     */
    public function consultant_detail($id)
    {
        $consultant = Consultant::find($id);

        if (! $consultant) {
            return response()->json(['error' => 'Consultant not found.'], 404);
        }

        return response()->json([
            'first_name' => $consultant->first_name,
            'last_name' => $consultant->last_name,
            'email' => $consultant->email,
            'phone' => $consultant->phone,
        ]);
    }

    /**
     * Detach a consultant from an HB837 record.
     */
    public function detachConsultants($id)
    {
        $hb837 = HB837::findOrFail($id);
        $hb837->update(['assigned_consultant_id' => null]);

        return redirect()->route('admin.hb837.edit', $id)
            ->with('success', 'Consultant detached successfully.');
    }

    /**
     * Export consultants data in various formats.
     */
    public function export(Request $request)
    {
        try {
            $format = strtolower($request->get('format', 'excel')); // Default to Excel
            $includeFiles = $request->boolean('include_files', false);
            $filename = 'consultants_'.date('Y-m-d_H-i-s');

            Log::info('Exporting consultants', [
                'format' => $format,
                'include_files' => $includeFiles,
                'filename' => $filename,
            ]);

            switch ($format) {
                case 'excel':
                case 'xlsx':
                    return Excel::download(
                        new ConsultantExport($includeFiles),
                        $filename.'.xlsx'
                    );

                case 'csv':
                    return Excel::download(
                        new ConsultantExport($includeFiles),
                        $filename.'.csv',
                        \Maatwebsite\Excel\Excel::CSV
                    );

                case 'json':
                    return $this->exportJson();

                default:
                    Log::warning('Unsupported export format requested', ['format' => $format]);

                    return back()->with('error', 'Unsupported export format: '.$format);
            }
        } catch (\Exception $e) {
            Log::error('Failed to export consultants data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Export failed: '.$e->getMessage());
        }
    }

    /**
     * Export consultants data as JSON.
     */
    protected function exportJson()
    {
        try {
            // Retrieve all consultants data with relationships
            $consultants = Consultant::with('files')->get();

            Log::info('Preparing JSON export', ['consultants_count' => $consultants->count()]);

            // Define the storage path for the JSON file
            $jsonFilePath = 'database/seeds/data/consultants.json';

            // Ensure the target directory exists
            $directory = dirname($jsonFilePath);

            if (! Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
                Log::info('Created directory for JSON export', ['directory' => $directory]);
            }

            // Convert data to pretty JSON format
            $jsonData = $consultants->toJson(JSON_PRETTY_PRINT);

            // Write the JSON file to storage
            if (! Storage::disk('local')->put($jsonFilePath, $jsonData)) {
                throw new \Exception('Failed to write JSON file to storage.');
            }

            Log::info('Consultants data exported successfully', ['path' => $jsonFilePath]);

            // Return download response
            $fullPath = Storage::disk('local')->path($jsonFilePath);
            $downloadName = 'consultants_'.date('Y-m-d_H-i-s').'.json';

            return response()->download($fullPath, $downloadName);
        } catch (\Exception $e) {
            Log::error('Failed to export consultants JSON data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate consultant data.
     */
    private function validateConsultant(Request $request, $id = null)
    {
        return $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                $id ? Rule::unique('consultants')->ignore($id) : Rule::unique('consultants'),
                'max:255',
            ],
            'email.unique' => 'The email address is already registered. Please use a different email.',
            'dba_company_name' => 'nullable|string|max:255',
            'mailing_address' => 'nullable|string|max:255',
            'fcp_expiration_date' => 'nullable|date',
            'assigned_light_meter' => 'nullable|string|max:255',
            'lm_nist_expiration_date' => 'nullable|date',
            'subcontractor_bonus_rate' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'files.*' => 'nullable|file|max:2048',
        ]);

    }

    /**
     * Handle file uploads.
     */
    private function uploadFiles(Request $request, Consultant $consultant)
    {
        foreach ($request->file('files') as $file) {
            $filePath = $file->store('consultant_files', 'public');

            $consultant->files()->create([
                'file_type' => $file->getClientMimeType(),
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
            ]);
        }
    }

    /**
     * Handle file deletions.
     */
    private function deleteFiles(array $fileIds, Consultant $consultant)
    {
        foreach ($fileIds as $fileId) {
            $file = $consultant->files()->find($fileId);
            if ($file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
        }
    }
}
