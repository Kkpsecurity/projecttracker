<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Backup;

class BackupModelTest extends TestCase
{
    /**
     * Test Backup model can be instantiated
     */
    public function test_backup_model_can_be_instantiated(): void
    {
        $backup = new Backup();
        $this->assertInstanceOf(Backup::class, $backup);
    }

    /**
     * Test Backup model has correct fillable fields
     */
    public function test_backup_model_has_correct_fillable_fields(): void
    {
        $backup = new Backup();
        $expected = [
            'uuid', 'name', 'tables', 'user_id', 'filename', 'size', 'record_count', 'status'
        ];
        
        $this->assertEquals($expected, $backup->getFillable());
    }

    /**
     * Test Backup model casts tables as array
     */
    public function test_backup_model_casts_tables_as_array(): void
    {
        $backup = new Backup();
        $casts = $backup->getCasts();
        
        $this->assertArrayHasKey('tables', $casts);
        $this->assertEquals('array', $casts['tables']);
    }

    /**
     * Test Backup model attributes are properly accessible
     */
    public function test_backup_attributes_are_accessible(): void
    {
        $backup = new Backup([
            'uuid' => 'test-uuid',
            'name' => 'Test Name',
            'tables' => ['table1', 'table2'],
            'status' => 'pending'
        ]);

        $this->assertEquals('test-uuid', $backup->uuid);
        $this->assertEquals('Test Name', $backup->name);
        $this->assertEquals(['table1', 'table2'], $backup->tables);
        $this->assertEquals('pending', $backup->status);
    }

    /**
     * Test Backup model has user relationship method
     */
    public function test_backup_has_user_relationship(): void
    {
        $backup = new Backup();
        $this->assertTrue(method_exists($backup, 'user'));
    }

    /**
     * Test Backup model table name
     */
    public function test_backup_model_table_name(): void
    {
        $backup = new Backup();
        $this->assertEquals('backups', $backup->getTable());
    }

    /**
     * Test Backup model uses timestamps
     */
    public function test_backup_model_uses_timestamps(): void
    {
        $backup = new Backup();
        $this->assertTrue($backup->usesTimestamps());
    }
}
