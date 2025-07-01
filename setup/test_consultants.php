<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Consultant;
use App\Models\HB837;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Consultant Records Analysis\n";
echo "===========================\n\n";

$consultants = Consultant::all();
echo "Total consultants: " . $consultants->count() . "\n\n";

foreach ($consultants as $consultant) {
    echo "ID: {$consultant->id}\n";
    echo "Name: {$consultant->first_name} {$consultant->last_name}\n";
    echo "Email: {$consultant->email}\n";
    echo "Company: " . ($consultant->dba_company_name ?: 'N/A') . "\n";

    // Check assignments
    $activeAssignments = HB837::where('assigned_consultant_id', $consultant->id)
        ->whereNotIn('report_status', ['completed'])
        ->count();

    $completedAssignments = HB837::where('assigned_consultant_id', $consultant->id)
        ->where('report_status', 'completed')
        ->count();

    echo "Active assignments: {$activeAssignments}\n";
    echo "Completed assignments: {$completedAssignments}\n";
    echo "Files: " . $consultant->files()->count() . "\n";
    echo "---\n\n";
}
