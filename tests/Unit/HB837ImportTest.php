<?php

namespace Tests\Unit;

use App\Imports\HB837Import;
use PHPUnit\Framework\TestCase;

class HB837ImportTest extends TestCase
{
    protected HB837Import $hb837Import;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hb837Import = new HB837Import;
    }

    /**
     * Test HB837Import can be instantiated
     */
    public function test_hb837_import_can_be_instantiated(): void
    {
        $this->assertInstanceOf(HB837Import::class, $this->hb837Import);
    }

    /**
     * Test HB837Import implements required interfaces
     */
    public function test_hb837_import_implements_required_interfaces(): void
    {
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\ToModel::class, $this->hb837Import);
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithHeadingRow::class, $this->hb837Import);
    }

    /**
     * Test HB837Import has counter properties
     */
    public function test_hb837_import_has_counter_properties(): void
    {
        $this->assertObjectHasProperty('importedCount', $this->hb837Import);
        $this->assertObjectHasProperty('updatedCount', $this->hb837Import);
        $this->assertObjectHasProperty('skippedCount', $this->hb837Import);
        $this->assertObjectHasProperty('skippedProperties', $this->hb837Import);

        // Check default values
        $this->assertEquals(0, $this->hb837Import->importedCount);
        $this->assertEquals(0, $this->hb837Import->updatedCount);
        $this->assertEquals(0, $this->hb837Import->skippedCount);
        $this->assertIsArray($this->hb837Import->skippedProperties);
    }

    /**
     * Test setTruncateMode method
     */
    public function test_set_truncate_mode(): void
    {
        $this->assertTrue(method_exists($this->hb837Import, 'setTruncateMode'));

        $result = $this->hb837Import->setTruncateMode(true);
        $this->assertInstanceOf(HB837Import::class, $result);

        $result = $this->hb837Import->setTruncateMode(false);
        $this->assertInstanceOf(HB837Import::class, $result);
    }

    /**
     * Test model method exists
     */
    public function test_model_method_exists(): void
    {
        $this->assertTrue(method_exists($this->hb837Import, 'model'));
    }

    /**
     * Test import counters are numeric
     */
    public function test_import_counters_are_numeric(): void
    {
        $this->assertIsNumeric($this->hb837Import->importedCount);
        $this->assertIsNumeric($this->hb837Import->updatedCount);
        $this->assertIsNumeric($this->hb837Import->skippedCount);
    }

    /**
     * Test truncate mode flag exists and is accessible
     */
    public function test_truncate_mode_flag_is_accessible(): void
    {
        $reflection = new \ReflectionClass($this->hb837Import);
        $this->assertTrue($reflection->hasProperty('truncateMode'));

        $property = $reflection->getProperty('truncateMode');
        $property->setAccessible(true);

        // Default should be false
        $this->assertFalse($property->getValue($this->hb837Import));

        // Set to true and verify
        $this->hb837Import->setTruncateMode(true);
        $this->assertTrue($property->getValue($this->hb837Import));
    }

    /**
     * Test fields property exists and is array
     */
    public function test_fields_property_exists_and_is_array(): void
    {
        $reflection = new \ReflectionClass($this->hb837Import);
        $this->assertTrue($reflection->hasProperty('fields'));

        $property = $reflection->getProperty('fields');
        $property->setAccessible(true);
        $fields = $property->getValue($this->hb837Import);

        $this->assertIsArray($fields);
    }

    /**
     * Test that import has required Laravel Excel methods
     */
    public function test_has_required_laravel_excel_methods(): void
    {
        $this->assertTrue(method_exists($this->hb837Import, 'model'));
        // headingRow is implemented through interface, not as a method
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithHeadingRow::class, $this->hb837Import);
    }
}
