<?php

namespace Tests\Unit;

use App\Services\ImportService;
use PHPUnit\Framework\TestCase;

class ImportServiceTest extends TestCase
{
    protected ImportService $importService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService;
    }

    /**
     * Test that ImportService can be instantiated
     */
    public function test_import_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(ImportService::class, $this->importService);
    }

    /**
     * Test that ImportService has required methods
     */
    public function test_import_service_has_required_methods(): void
    {
        $this->assertTrue(method_exists($this->importService, 'previewImport'));
        $this->assertTrue(method_exists($this->importService, 'executeImport'));
    }

    /**
     * Test getExpectedHeaders method exists and is callable
     */
    public function test_get_expected_headers_method_exists(): void
    {
        $reflection = new \ReflectionClass($this->importService);
        $this->assertTrue($reflection->hasMethod('getExpectedHeaders'));

        $method = $reflection->getMethod('getExpectedHeaders');
        $this->assertTrue($method->isProtected());
    }

    /**
     * Test validateHeaders method exists and is callable
     */
    public function test_validate_headers_method_exists(): void
    {
        $reflection = new \ReflectionClass($this->importService);
        $this->assertTrue($reflection->hasMethod('validateHeaders'));

        $method = $reflection->getMethod('validateHeaders');
        $this->assertTrue($method->isProtected());
    }

    /**
     * Test import preview method signature
     */
    public function test_preview_import_method_signature(): void
    {
        $reflection = new \ReflectionClass($this->importService);
        $method = $reflection->getMethod('previewImport');

        $this->assertEquals(1, $method->getNumberOfParameters());
        $this->assertEquals('filePath', $method->getParameters()[0]->getName());
    }

    /**
     * Test execute import method signature
     */
    public function test_execute_import_method_signature(): void
    {
        $reflection = new \ReflectionClass($this->importService);
        $method = $reflection->getMethod('executeImport');

        $this->assertEquals(1, $method->getNumberOfParameters());
        $this->assertEquals('filePath', $method->getParameters()[0]->getName());
    }
}
