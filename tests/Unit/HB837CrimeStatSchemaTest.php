<?php

namespace Tests\Unit;

use App\Services\HB837\HB837CrimeStatSchema;
use Tests\TestCase;

class HB837CrimeStatSchemaTest extends TestCase
{
    public function test_normalize_forces_expected_arrays(): void
    {
        $stats = [
            'schema_version' => 1,
            'source' => [
                'vendor' => 'SecurityGauge',
            ],
            'report' => [
                'period' => [],
                'location' => [],
            ],
            'summary' => [
                'crime_risk' => 'Moderate',
                'notes' => 'not-an-array',
            ],
            'tables' => [
                'offenses' => 'not-an-array',
                'calls_for_service' => null,
                'comparisons' => null,
            ],
            'raw' => [
                'text' => null,
                'warnings' => 'not-an-array',
            ],
        ];

        $normalized = HB837CrimeStatSchema::normalize($stats);

        $this->assertIsArray($normalized);
        $this->assertIsArray($normalized['summary']['notes']);
        $this->assertIsArray($normalized['tables']['offenses']);
        $this->assertIsArray($normalized['tables']['calls_for_service']);
        $this->assertIsArray($normalized['tables']['comparisons']);
        $this->assertIsArray($normalized['raw']['warnings']);
    }

    public function test_validate_v1_accepts_minimal_payload_shape(): void
    {
        $stats = [
            'schema_version' => 1,
            'source' => [
                'vendor' => 'SecurityGauge',
            ],
            'report' => [
                'title' => 'Example',
                'generated_at' => '2025-04-18',
                'period' => [
                    'start' => '2025-04-18',
                    'end' => '2025-04-18',
                ],
                'location' => [
                    'state' => 'FL',
                ],
            ],
            'summary' => [
                'crime_risk' => 'Moderate',
                'notes' => [],
            ],
            'tables' => [
                'offenses' => [],
                'calls_for_service' => [],
                'comparisons' => [],
            ],
            'raw' => [
                'text' => null,
                'warnings' => [],
            ],
        ];

        $validated = HB837CrimeStatSchema::validateV1($stats);

        $this->assertSame(1, $validated['schema_version']);
        $this->assertSame('2025-04-18', $validated['report']['generated_at']);
        $this->assertSame('Moderate', $validated['summary']['crime_risk']);
    }
}
