<?php

// Usage:
//   php scripts/dev/seed_hb837_risk_measures.php 29
//
// Seeds a couple of curated Section 4 risk measures for a given HB837 record,
// so the PDF Section 4 will prefer curated measures (Option B).

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$hb837Id = isset($argv[1]) ? (int) $argv[1] : 0;
if ($hb837Id <= 0) {
    fwrite(STDERR, "HB837 id is required. Example: php scripts/dev/seed_hb837_risk_measures.php 29\n");
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
        'section' => '4.1',
        'measure_no' => 1,
        'cb_rank' => 'CB1',
        'measure' => 'Install or verify functioning exterior lighting at primary pedestrian routes, parking areas, and building entries; replace non-functioning fixtures promptly.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'section' => '4.3',
        'measure_no' => 1,
        'cb_rank' => 'CB2',
        'measure' => 'Improve line-of-sight in common outdoor areas by trimming landscaping near walkways/entries and maintaining clear visibility around corners and concealment points.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'section' => '4.2',
        'measure_no' => 1,
        'cb_rank' => 'CB2',
        'measure' => 'Verify CCTV coverage and recording retention for key areas (entries/exits, parking, common areas); repair non-functioning cameras and ensure time/date stamps are correct.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'section' => '4.4',
        'measure_no' => 1,
        'cb_rank' => 'CB1',
        'measure' => 'Inspect and maintain doors, locks, and window hardware for residential buildings/units; repair deficiencies to ensure secure closure and proper access control.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'section' => '4.5',
        'measure_no' => 1,
        'cb_rank' => 'CB3',
        'measure' => 'Evaluate access control and visitor management practices for the leasing office, mail areas, laundry rooms, and other community spaces; post clear rules and restrict unauthorized access where feasible.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'section' => '4.6',
        'measure_no' => 1,
        'cb_rank' => 'CB2',
        'measure' => 'Implement routine security checks and incident reporting procedures; train staff on escalation and document follow-up actions to support consistent enforcement and community engagement.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
];

// Idempotent-ish: remove existing sample rows with these exact section+measure_no pairs.
\Illuminate\Support\Facades\DB::table('hb837_risk_measures')
    ->where('hb837_id', $hb837Id)
    ->where(function ($q) {
        $q
            ->where(function ($q2) {
                $q2->where('section', '4.1')->where('measure_no', 1);
            })
            ->orWhere(function ($q2) {
                $q2->where('section', '4.2')->where('measure_no', 1);
            })
            ->orWhere(function ($q2) {
                $q2->where('section', '4.3')->where('measure_no', 1);
            })
            ->orWhere(function ($q2) {
                $q2->where('section', '4.4')->where('measure_no', 1);
            })
            ->orWhere(function ($q2) {
                $q2->where('section', '4.5')->where('measure_no', 1);
            })
            ->orWhere(function ($q2) {
                $q2->where('section', '4.6')->where('measure_no', 1);
            });
    })
    ->delete();

\Illuminate\Support\Facades\DB::table('hb837_risk_measures')->insert($rows);

echo "Seeded 6 risk measures for HB837 id={$hb837Id}\n";
