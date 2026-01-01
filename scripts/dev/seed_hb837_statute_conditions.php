<?php

// Usage:
//   php scripts/dev/seed_hb837_statute_conditions.php 29
//
// Seeds curated statute condition statuses/observations for a given HB837 record,
// so PDF Section 2 renders meaningful values.

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$hb837Id = isset($argv[1]) ? (int) $argv[1] : 0;
if ($hb837Id <= 0) {
    fwrite(STDERR, "HB837 id is required. Example: php scripts/dev/seed_hb837_statute_conditions.php 29\n");
    exit(2);
}

$hb837 = \App\Models\HB837::query()->find($hb837Id);
if (!$hb837) {
    fwrite(STDERR, "HB837 id={$hb837Id} not found.\n");
    exit(1);
}

$now = now();

$rows = [
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'condition_key' => 'cctv_system',
        'status' => 'compliant',
        'observations' => 'CCTV cameras were observed at primary entries/exits. Management confirmed footage is retained and accessible for review.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'condition_key' => 'parking_lot_illumination',
        'status' => 'unknown',
        'observations' => 'Lighting appears generally adequate; foot-candle measurements were not performed during this assessment.',
        'sort_order' => 20,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'condition_key' => 'other_lighting',
        'status' => 'non_compliant',
        'observations' => 'Several common-area fixtures were observed non-functional during the walkthrough. Replace/repair fixtures and confirm dusk-to-dawn controls are operational.',
        'sort_order' => 30,
        'created_at' => $now,
        'updated_at' => $now,
    ],
];

\Illuminate\Support\Facades\DB::table('hb837_statute_conditions')
    ->where('hb837_id', $hb837Id)
    ->whereIn('condition_key', collect($rows)->pluck('condition_key')->all())
    ->delete();

\Illuminate\Support\Facades\DB::table('hb837_statute_conditions')->insert($rows);

echo "Seeded " . count($rows) . " statute condition rows for HB837 id={$hb837Id}\n";
