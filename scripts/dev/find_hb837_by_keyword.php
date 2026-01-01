<?php

// Usage:
//   php scripts/dev/find_hb837_by_keyword.php Waverly
//   php scripts/dev/find_hb837_by_keyword.php "Summit Pines"
//
// Boots Laravel and prints the latest matching HB837 record.

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$keyword = $argv[1] ?? 'Waverly';
$keyword = trim((string) $keyword);

if ($keyword === '') {
    fwrite(STDERR, "Keyword is required.\n");
    exit(2);
}

$query = \App\Models\HB837::query()
    ->where('property_name', 'like', '%' . $keyword . '%')
    ->orWhere('address', 'like', '%' . $keyword . '%')
    ->orderByDesc('id');

$record = $query->first();

if (!$record) {
    echo "NOT_FOUND\n";

    $recent = \App\Models\HB837::query()
        ->orderByDesc('id')
        ->limit(15)
        ->get(['id', 'property_name', 'address']);

    foreach ($recent as $r) {
        echo $r->id . " | " . ($r->property_name ?? '') . " | " . ($r->address ?? '') . "\n";
    }

    exit(1);
}

echo "FOUND\n";
echo "id=" . $record->id . "\n";
echo "property_name=" . ($record->property_name ?? '') . "\n";
echo "address=" . ($record->address ?? '') . "\n";
