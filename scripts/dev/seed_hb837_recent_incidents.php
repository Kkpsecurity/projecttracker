<?php

// Usage:
//   php scripts/dev/seed_hb837_recent_incidents.php 29
//
// Seeds a few curated Recent Incidents for a given HB837 record,
// so the PDF Section 1.3 will render meaningful rows.

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$hb837Id = isset($argv[1]) ? (int) $argv[1] : 0;
if ($hb837Id <= 0) {
    fwrite(STDERR, "HB837 id is required. Example: php scripts/dev/seed_hb837_recent_incidents.php 29\n");
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
        'incident_date' => '2024-2025',
        'summary' => 'Multiple vehicle break-ins were reported in the parking areas during overnight hours. Law enforcement was notified and patrols were requested; management increased lighting checks and encouraged residents to remove valuables from vehicles.',
        'sort_order' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'incident_date' => 'Summer 2025',
        'summary' => 'A resident reported an attempted unauthorized entry at a ground-floor unit. The incident was reported to law enforcement; maintenance inspected door hardware and repaired a misaligned strike plate to improve secure closure.',
        'sort_order' => 20,
        'created_at' => $now,
        'updated_at' => $now,
    ],
    [
        'hb837_id' => $hb837Id,
        'created_by' => null,
        'incident_date' => 'Fall 2025',
        'summary' => 'A disturbance occurred in a common area after hours. Staff documented the incident, reminded residents of community rules, and reviewed camera coverage for the area to confirm footage availability.',
        'sort_order' => 30,
        'created_at' => $now,
        'updated_at' => $now,
    ],
];

// Idempotent-ish: remove rows matching these (date, sort_order) pairs for this HB837.
\Illuminate\Support\Facades\DB::table('hb837_recent_incidents')
    ->where('hb837_id', $hb837Id)
    ->where(function ($q) {
        $q
            ->where(function ($q2) {
                $q2->where('incident_date', '2024-2025')->where('sort_order', 10);
            })
            ->orWhere(function ($q2) {
                $q2->where('incident_date', 'Summer 2025')->where('sort_order', 20);
            })
            ->orWhere(function ($q2) {
                $q2->where('incident_date', 'Fall 2025')->where('sort_order', 30);
            });
    })
    ->delete();

\Illuminate\Support\Facades\DB::table('hb837_recent_incidents')->insert($rows);

echo "Seeded 3 recent incidents for HB837 id={$hb837Id}\n";
