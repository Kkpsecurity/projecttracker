<?php

namespace App\Http\Controllers\Admin\HB837;

use App\Http\Controllers\Controller;
use App\Services\ImportService;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');
        $path = $file->store('imports/previews');

        try {
            $preview = $this->importService->previewImport($path);
            return response()->json($preview);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Preview failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function execute(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'confirmed' => 'sometimes|boolean'
        ]);

        if (!$request->confirmed) {
            return response()->json([
                'error' => 'Confirmation required',
                'message' => 'Please confirm the import after preview'
            ], 400);
        }

        $file = $request->file('file');
        $path = $file->store('imports/processing');

        try {
            $result = $this->importService->executeImport($path);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Import failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
