<?php

namespace App\Console\Commands;

use App\Models\HB837;
use App\Models\HB837File;
use App\Services\HB837\HB837CrimePdfExtractionService;
use App\Services\HB837\HB837ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class HB837SmokeTestCommand extends Command
{
    protected $signature = 'hb837:smoke-test
                            {hb837_id? : HB837 record id (optional; will auto-pick one if omitted)}
                            {--crime-pdf= : Path to SecurityGauge crime report PDF (defaults to docs/newtask/SG-Crime-Report*.pdf)}';

    protected $description = 'Runs a Phase 5 smoke test: upload crime PDF, extract crime stats, generate & attach the latest PDF report.';

    public function handle(HB837CrimePdfExtractionService $extractionService, HB837ReportService $reportService): int
    {
        $hb837IdArg = $this->argument('hb837_id');
        $hb837Id = is_null($hb837IdArg) ? null : (int) $hb837IdArg;

        $hb837 = $hb837Id
            ? HB837::query()->find($hb837Id)
            : HB837::query()->orderBy('id')->first();

        if (!$hb837) {
            $this->error('No HB837 records found to run the smoke test.');
            return self::FAILURE;
        }

        $crimePdfPath = (string) ($this->option('crime-pdf') ?? '');
        if (trim($crimePdfPath) === '') {
            $matches = glob(base_path('docs/newtask/SG-Crime-Report*.pdf')) ?: [];
            if (!empty($matches)) {
                $crimePdfPath = $matches[0];
            }
        }

        if (trim($crimePdfPath) === '' || !is_file($crimePdfPath)) {
            $this->error('Crime PDF file not found. Provide --crime-pdf="C:\\path\\to\\file.pdf" or ensure docs/newtask/SG-Crime-Report*.pdf exists.');
            return self::FAILURE;
        }

        $this->info('HB837: ' . $hb837->id . ' — ' . ($hb837->property_name ?? 'N/A'));
        $this->info('Using crime PDF: ' . $crimePdfPath);

        // 1) Upload crime report as an hb837_files row
        $timestamp = now()->format('Ymd_His');
        $storedFilename = 'smoke_crime_report_' . $timestamp . '.pdf';
        $storedPath = 'hb837/' . $hb837->id . '/' . $storedFilename;

        $bytes = file_get_contents($crimePdfPath);
        if ($bytes === false || $bytes === '') {
            $this->error('Failed to read crime PDF bytes.');
            return self::FAILURE;
        }

        Storage::disk('public')->put($storedPath, $bytes);

        $hb837File = HB837File::create([
            'hb837_id' => $hb837->id,
            'uploaded_by' => null,
            'filename' => $storedFilename,
            'original_filename' => basename($crimePdfPath),
            'file_path' => $storedPath,
            'mime_type' => 'application/pdf',
            'file_size' => strlen($bytes),
            'file_category' => 'crime_report',
            'description' => 'Smoke test crime report upload',
        ]);

        $this->info('Uploaded crime report hb837_files.id=' . $hb837File->id);

        // 2) Extract crime stats
        $crimeStat = $extractionService->extractAndUpsert($hb837File);
        $warnings = (array) data_get($crimeStat->stats, 'raw.warnings', []);
        $this->info('Extracted hb837_crime_stats.id=' . $crimeStat->id . ' risk=' . ($crimeStat->crime_risk ?? 'N/A') . ' warnings=' . count($warnings));

        // 3) Generate & persist latest PDF report (one project, one file)
        $result = $reportService->generateAndPersistProjectPdf($hb837);
        $this->info('Generated report saved: ' . $result['stored_path']);
        $this->info('generated_report hb837_files.id=' . $result['hb837_file']->id);

        return self::SUCCESS;
    }
}
