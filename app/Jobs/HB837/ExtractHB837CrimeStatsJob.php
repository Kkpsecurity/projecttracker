<?php

namespace App\Jobs\HB837;

use App\Models\HB837File;
use App\Services\HB837\HB837CrimePdfExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExtractHB837CrimeStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $hb837FileId)
    {
    }

    public function handle(HB837CrimePdfExtractionService $service): void
    {
        $file = HB837File::query()->find($this->hb837FileId);
        if (!$file) {
            Log::warning('ExtractHB837CrimeStatsJob: file not found', ['hb837_file_id' => $this->hb837FileId]);
            return;
        }

        if (($file->file_category ?? null) !== 'crime_report') {
            Log::info('ExtractHB837CrimeStatsJob: skipped (not crime_report)', ['hb837_file_id' => $file->id]);
            return;
        }

        try {
            $crimeStat = $service->extractAndUpsert($file);
            Log::info('ExtractHB837CrimeStatsJob: extracted', [
                'hb837_file_id' => $file->id,
                'hb837_id' => $file->hb837_id,
                'hb837_crime_stat_id' => $crimeStat->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('ExtractHB837CrimeStatsJob: extraction failed', [
                'hb837_file_id' => $file->id,
                'hb837_id' => $file->hb837_id,
                'error' => $e->getMessage(),
            ]);

            // Let the job fail for visibility/retries.
            throw $e;
        }
    }
}
