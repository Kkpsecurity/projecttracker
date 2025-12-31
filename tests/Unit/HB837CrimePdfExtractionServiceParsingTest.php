<?php

namespace Tests\Unit;

use App\Services\HB837\HB837CrimePdfExtractionService;
use Carbon\Carbon;
use Tests\TestCase;

class HB837CrimePdfExtractionServiceParsingTest extends TestCase
{
    public function test_parses_crime_risk_from_crime_risk_summary_block(): void
    {
        $service = new HB837CrimePdfExtractionService();

        $text = "Some header\nCrime Risk Summary\n...\nMODERATE\nMore text";

        $method = new \ReflectionMethod($service, 'extractCrimeRisk');
        $method->setAccessible(true);

        $risk = $method->invoke($service, $text);

        $this->assertSame('Moderate', $risk);
    }

    public function test_parses_report_date_with_weekday_format(): void
    {
        $service = new HB837CrimePdfExtractionService();

        $text = "Friday, April 18, 2025\nCrime Risk Summary\nMODERATE";

        $method = new \ReflectionMethod($service, 'extractReportDate');
        $method->setAccessible(true);

        /** @var Carbon|null $date */
        $date = $method->invoke($service, $text);

        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertSame('2025-04-18', $date->toDateString());
    }

    public function test_period_falls_back_to_report_date_when_no_range_exists(): void
    {
        $service = new HB837CrimePdfExtractionService();

        $text = "Friday, April 18, 2025\nCrime Risk Summary\nMODERATE";

        $method = new \ReflectionMethod($service, 'extractPeriodDates');
        $method->setAccessible(true);

        /** @var array{0: Carbon|null, 1: Carbon|null} $period */
        $period = $method->invoke($service, $text);

        $this->assertInstanceOf(Carbon::class, $period[0]);
        $this->assertInstanceOf(Carbon::class, $period[1]);
        $this->assertSame('2025-04-18', $period[0]->toDateString());
        $this->assertSame('2025-04-18', $period[1]->toDateString());
    }
}
