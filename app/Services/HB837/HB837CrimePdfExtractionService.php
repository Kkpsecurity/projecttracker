<?php

namespace App\Services\HB837;

use App\Models\HB837;
use App\Models\HB837CrimeStat;
use App\Models\HB837File;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class HB837CrimePdfExtractionService
{
    /**
     * Extract crime stats from a SecurityGauge crime report PDF file.
     *
     * Best-effort extraction:
     * - Always stores raw text for review/debug
     * - Populates columns where possible (period, crime_risk)
     * - Writes canonical v1 JSON payload
     */
    public function extractAndUpsert(HB837File $crimePdfFile): HB837CrimeStat
    {
        $hb837 = $crimePdfFile->hb837;
        if (!$hb837 instanceof HB837) {
            throw new \RuntimeException('HB837 record not found for file id ' . $crimePdfFile->id);
        }

        $warnings = [];

        if (($crimePdfFile->mime_type ?? '') !== 'application/pdf') {
            $warnings[] = 'File mime_type is not application/pdf; continuing anyway.';
        }

        $pdfPath = Storage::disk('public')->path($crimePdfFile->file_path);
        if (!is_file($pdfPath)) {
            throw new \RuntimeException('PDF file not found on disk: ' . $pdfPath);
        }

        $sha256 = @hash_file('sha256', $pdfPath) ?: null;

        $rawText = $this->extractTextWithPdftotext($pdfPath);
        if (trim($rawText) === '') {
            $warnings[] = 'pdftotext returned empty output (PDF may be scanned/image-only).';
        }

        $reportDate = $this->extractReportDate($rawText);

        $crimeRisk = $this->extractCrimeRisk($rawText);
        if ($crimeRisk === null) {
            $warnings[] = 'Could not detect crime risk level in PDF text.';
        }

        [$periodStart, $periodEnd] = $this->extractPeriodDates($rawText);
        if ($periodStart === null || $periodEnd === null) {
            $warnings[] = 'Could not detect reporting period start/end in PDF text.';
        }

        $offenses = $this->extractOffenseCounts($rawText);
        if (empty($offenses)) {
            $warnings[] = 'Could not detect offense counts table in PDF text.';
        }

        $stats = HB837CrimeStatSchema::normalize([
            'schema_version' => 1,
            'source' => [
                'vendor' => 'SecurityGauge',
                'hb837_file_id' => $crimePdfFile->id,
                'filename' => $crimePdfFile->original_filename,
                'sha256' => $sha256,
            ],
            'report' => [
                'title' => $crimePdfFile->original_filename,
                'generated_at' => $reportDate?->toDateString(),
                'period' => [
                    'start' => $periodStart?->toDateString(),
                    'end' => $periodEnd?->toDateString(),
                ],
                'location' => [
                    'address' => $hb837->address,
                    'city' => $hb837->city,
                    'state' => $hb837->state,
                    'zip' => $hb837->zip,
                ],
            ],
            'summary' => [
                'crime_risk' => $crimeRisk,
                'notes' => [],
            ],
            'tables' => [
                'offenses' => $offenses,
                'calls_for_service' => [],
                'comparisons' => [],
            ],
            'raw' => [
                'text' => $rawText,
                'warnings' => $warnings,
            ],
        ], [
            'hb837_file_id' => $crimePdfFile->id,
            'filename' => $crimePdfFile->original_filename,
            'sha256' => $sha256,
        ]);

        // Upsert by hb837_id (unique)
        $crimeStat = HB837CrimeStat::query()->firstOrNew(['hb837_id' => $hb837->id]);
        $crimeStat->fill([
            'hb837_file_id' => $crimePdfFile->id,
            'source' => 'SecurityGauge',
            'report_title' => $crimePdfFile->original_filename,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'crime_risk' => $crimeRisk,
            'stats' => $stats,
            'is_reviewed' => false,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
        $crimeStat->save();

        // Also copy the extracted risk into the primary HB837 field for easy filtering/visibility.
        if ($crimeRisk !== null && ($hb837->securitygauge_crime_risk ?? null) !== $crimeRisk) {
            $hb837->securitygauge_crime_risk = $crimeRisk;
            $hb837->save();
        }

        return $crimeStat;
    }

    private function extractTextWithPdftotext(string $pdfPath): string
    {
        $binary = config('hb837.pdftotext_binary', 'pdftotext');

        // Output to stdout by using '-' as output file.
        $process = new Process([
            $binary,
            '-layout',
            '-enc',
            'UTF-8',
            $pdfPath,
            '-',
        ]);

        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return (string) $process->getOutput();
    }

    private function extractCrimeRisk(string $text): ?string
    {
        // SecurityGauge's crime risk label often appears as a standalone word
        // near the "Crime Risk Summary" header (e.g., "MODERATE").
        $patterns = [
            '/Crime\s*Risk\s*Summary[\s\S]{0,400}\b(LOW|MODERATE|ELEVATED|HIGH|SEVERE)\b/i',
            // Fallback for variants like "Crime Risk: Moderate"
            '/Crime\s*Risk(?:\s*Level)?\s*[:\-]?\s*(Low|Moderate|Elevated|High|Severe)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return ucfirst(strtolower($m[1]));
            }
        }

        return null;
    }

    private function extractReportDate(string $text): ?Carbon
    {
        // Example in SecurityGauge reports: "Friday, April 18, 2025"
        if (preg_match('/\b(?:Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday),\s*([A-Za-z]{3,9}\s+\d{1,2},\s+\d{4})\b/i', $text, $m)) {
            return $this->tryParseDate(trim($m[0]));
        }

        // Fallback: any first Month d, yyyy occurrence.
        if (preg_match('/\b([A-Za-z]{3,9}\s+\d{1,2},\s+\d{4})\b/', $text, $m)) {
            return $this->tryParseDate($m[1]);
        }

        return null;
    }

    /**
     * @return array{0: Carbon|null, 1: Carbon|null}
     */
    private function extractPeriodDates(string $text): array
    {
        // Try mm/dd/yyyy to mm/dd/yyyy
        if (preg_match('/(\d{1,2}\/\d{1,2}\/\d{2,4})\s*(?:to|\-|–|—)\s*(\d{1,2}\/\d{1,2}\/\d{2,4})/i', $text, $m)) {
            return [
                $this->tryParseDate($m[1]),
                $this->tryParseDate($m[2]),
            ];
        }

        // Try Month d, yyyy to Month d, yyyy
        if (preg_match('/([A-Za-z]{3,9}\s+\d{1,2},\s+\d{4})\s*(?:to|\-|–|—)\s*([A-Za-z]{3,9}\s+\d{1,2},\s+\d{4})/i', $text, $m)) {
            return [
                $this->tryParseDate($m[1]),
                $this->tryParseDate($m[2]),
            ];
        }

        // SecurityGauge reports frequently include a single report date (not a range).
        // In that case, treat it as a point-in-time period (start=end=date).
        $reportDate = $this->extractReportDate($text);
        if ($reportDate !== null) {
            return [$reportDate, $reportDate];
        }

        return [null, null];
    }

    private function tryParseDate(string $value): ?Carbon
    {
        $value = trim($value);
        foreach (['m/d/Y', 'm/d/y', 'n/j/Y', 'n/j/y', 'l, F j, Y', 'D, F j, Y', 'l, M j, Y', 'D, M j, Y', 'M j, Y', 'F j, Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->startOfDay();
            } catch (\Throwable $e) {
                // continue
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Extract offense counts using a simple label->count heuristic.
     *
     * @return array<int, array{label:string, category:string|null, count:int|null, rate_per_1000:float|null}>
     */
    private function extractOffenseCounts(string $text): array
    {
        $map = [
            'homicide' => ['Homicide'],
            'rape' => ['Rape', 'Sex Offense'],
            'robbery' => ['Robbery'],
            'aggravated-assault' => ['Aggravated Assault', 'Agg Assault'],
            'burglary' => ['Burglary'],
            'larceny-theft' => ['Larceny', 'Larceny/Theft', 'Theft'],
            'motor-vehicle-theft' => ['Motor Vehicle Theft', 'Auto Theft'],
            'arson' => ['Arson'],
        ];

        $rowsByCategory = [];

        foreach ($map as $category => $labels) {
            foreach ($labels as $label) {
                // Look for: "Label    12" anywhere in the text.
                $pattern = '/\b' . preg_quote($label, '/') . '\b\s+([0-9]{1,6})\b/i';
                if (preg_match($pattern, $text, $m)) {
                    $count = (int) $m[1];
                    $rowsByCategory[$category] = [
                        'label' => $label,
                        'category' => $category,
                        'count' => $count,
                        'rate_per_1000' => null,
                    ];
                    break;
                }
            }
        }

        // Preserve stable ordering by configured category list.
        $ordered = [];
        $order = (array) config('hb837.crime_stats_offense_categories', []);
        if (empty($order)) {
            $order = array_keys($map);
        }

        foreach ($order as $category) {
            if (isset($rowsByCategory[$category])) {
                $ordered[] = $rowsByCategory[$category];
            }
        }

        // Unknown extra offenses are not extracted yet; that will come in later refinements.
        return $ordered;
    }
}
