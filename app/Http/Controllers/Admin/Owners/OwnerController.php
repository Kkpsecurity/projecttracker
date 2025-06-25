<?php

namespace App\Http\Controllers\Admin\Owners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::paginate(10);
        return view('admin.owners.show', compact('owners'));
    }

    public function create()
    {
        return view('admin.owners.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $this->validateOwner($request);
            Owner::create($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            // Optionally check if it's a duplicate email error before returning a custom error
            return redirect()->back()->withInput()->withErrors([
                'email' => 'This email is already registered for another owner. Please use a different email.',
            ]);
        }

        return redirect()->route('admin.owners.index')
            ->with('success', 'Owner created successfully.');
    }

    protected function validateOwner(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                $id ? Rule::unique('owners')->ignore($id) : Rule::unique('owners'),
                'max:255',
            ],
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'zip' => 'nullable|string|max:10',
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'This email is already registered for another owner. Please use a different email.',
        ]);
    }


    public function edit($id)
    {
        $owner = Owner::findOrFail($id);
        return view('admin.owners.edit', compact('owner'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $this->validateOwner($request, $id);
            $owner = Owner::findOrFail($id);
            $owner->update($validatedData);
        } catch (\Illuminate\Database\QueryException $e) {
            // Optionally check if it's a duplicate email error before returning a custom error
            return redirect()->back()->withInput()->withErrors([
                'email' => 'This email is already registered for another owner. Please use a different email.',
            ]);
        }

        return redirect()->route('admin.owners.index')
            ->with('success', 'Owner updated successfully.');
    }

    public function destroy($id)
    {
        $owner = Owner::findOrFail($id);
        $owner->delete();

        return redirect()->route('admin.owners.index')->with('success', 'Owner deleted successfully.');
    }

    public function owner_detail($id)
    {
        $owner = Owner::findOrFail($id);
        return view('admin.owners.detail', compact('owner'));
    }

    public function detachOwners($id)
    {
        $owner = Owner::findOrFail($id);
        // Assuming there is a relationship to detach
        $owner->relatedModel()->detach();

        return redirect()->route('admin.owners.index')->with('success', 'Owner detached successfully.');
    }

    public function export()
    {
        try {
            // Retrieve all owners data
            $owners = Owner::all();

            // Define the storage path for the JSON file
            $jsonFilePath = 'database/seeds/data/owners.json';

            // Ensure the target directory exists
            $directory = dirname($jsonFilePath);

            if (!Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
            }

            // Convert data to pretty JSON format
            $jsonData = $owners->toJson(JSON_PRETTY_PRINT);

            // Write the JSON file to storage
            if (!Storage::disk('local')->put($jsonFilePath, $jsonData)) {
                throw new \Exception("Failed to write JSON file to storage.");
            }

            Log::info("Owners data exported successfully.", ['path' => $jsonFilePath]);
            return response()->json(['message' => 'Export successful', 'path' => $jsonFilePath], 200);
        } catch (\Exception $e) {
            Log::error("Failed to export owners data.", ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Export failed', 'error' => $e->getMessage()], 500);
        }
    }
}
