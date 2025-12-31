<?php

namespace App\Services\HB837;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class HB837CrimeStatSchema
{
    public const VERSION = 1;

    /**
     * Normalize an incoming stats payload into the canonical v1 shape.
     *
     * This does not attempt to “interpret” values; it only guarantees structure.
     */
    public static function normalize(?array $stats, array $context = []): ?array
    {
        if ($stats === null) {
            return null;
        }

        $schema = config('hb837.crime_stats_schema_v1');
        if (!is_array($schema)) {
            $schema = ['schema_version' => self::VERSION];
        }

        // Shallow merge is not enough due to nested defaults.
        $normalized = $schema;

        // Allow passing in partial payloads; only set known sections.
        foreach (['source', 'report', 'summary', 'tables', 'raw'] as $key) {
            $value = Arr::get($stats, $key);
            if (is_array($value)) {
                $normalized[$key] = array_replace_recursive($normalized[$key] ?? [], $value);
            }
        }

        // Always set version.
        $normalized['schema_version'] = (int) (Arr::get($stats, 'schema_version') ?? self::VERSION);

        // Optional: wire audit context.
        if (isset($context['hb837_file_id'])) {
            Arr::set($normalized, 'source.hb837_file_id', $context['hb837_file_id']);
        }
        if (isset($context['filename'])) {
            Arr::set($normalized, 'source.filename', $context['filename']);
        }
        if (isset($context['sha256'])) {
            Arr::set($normalized, 'source.sha256', $context['sha256']);
        }

        // Defensive: force these to arrays.
        if (!is_array(Arr::get($normalized, 'summary.notes'))) {
            Arr::set($normalized, 'summary.notes', []);
        }
        if (!is_array(Arr::get($normalized, 'tables.offenses'))) {
            Arr::set($normalized, 'tables.offenses', []);
        }
        if (!is_array(Arr::get($normalized, 'tables.calls_for_service'))) {
            Arr::set($normalized, 'tables.calls_for_service', []);
        }
        if (!is_array(Arr::get($normalized, 'tables.comparisons'))) {
            Arr::set($normalized, 'tables.comparisons', []);
        }
        if (!is_array(Arr::get($normalized, 'raw.warnings'))) {
            Arr::set($normalized, 'raw.warnings', []);
        }

        return $normalized;
    }

    /**
     * Validation rules for the canonical v1 payload.
     *
     * Kept intentionally permissive (layout varies between PDFs).
     */
    public static function rulesV1(): array
    {
        $offenseCategories = config('hb837.crime_stats_offense_categories', []);

        return [
            'schema_version' => 'required|integer|in:1',

            'source' => 'required|array',
            'source.vendor' => 'nullable|string|max:255',
            'source.hb837_file_id' => 'nullable|integer',
            'source.filename' => 'nullable|string|max:255',
            'source.sha256' => 'nullable|string|max:128',

            'report' => 'required|array',
            'report.title' => 'nullable|string|max:255',
            'report.generated_at' => 'nullable|date',
            'report.period' => 'required|array',
            'report.period.start' => 'nullable|date',
            'report.period.end' => 'nullable|date|after_or_equal:report.period.start',
            'report.location' => 'required|array',
            'report.location.address' => 'nullable|string|max:500',
            'report.location.city' => 'nullable|string|max:255',
            'report.location.state' => 'nullable|string|max:2',
            'report.location.zip' => 'nullable|string|max:10',

            'summary' => 'required|array',
            'summary.crime_risk' => 'nullable|string|max:255',
            'summary.notes' => 'nullable|array',
            'summary.notes.*' => 'string|max:1000',

            'tables' => 'required|array',
            'tables.offenses' => 'nullable|array',
            'tables.offenses.*.label' => 'required_with:tables.offenses|string|max:255',
            'tables.offenses.*.category' => empty($offenseCategories)
                ? 'nullable|string|max:64'
                : 'nullable|string|in:' . implode(',', $offenseCategories),
            'tables.offenses.*.count' => 'nullable|integer|min:0',
            'tables.offenses.*.rate_per_1000' => 'nullable|numeric|min:0',

            'tables.calls_for_service' => 'nullable|array',
            'tables.comparisons' => 'nullable|array',

            'raw' => 'required|array',
            'raw.text' => 'nullable|string',
            'raw.warnings' => 'nullable|array',
            'raw.warnings.*' => 'string|max:1000',
        ];
    }

    /**
     * Validate and return the normalized payload.
     */
    public static function validateV1(array $stats, array $context = []): array
    {
        $normalized = self::normalize($stats, $context) ?? ['schema_version' => self::VERSION];

        $validator = Validator::make($normalized, self::rulesV1());
        $validator->validate();

        return $normalized;
    }
}
