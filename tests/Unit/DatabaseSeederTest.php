<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Database\Seeders\PostgreSQLToMySQLDataSeeder;
use Database\Seeders\DatabaseSeeder;

class DatabaseSeederTest extends TestCase
{
    /**
     * Test that PostgreSQL to MySQL seeder exists and can be instantiated.
     */
    public function test_postgresql_to_mysql_seeder_exists(): void
    {
        $seeder = new PostgreSQLToMySQLDataSeeder();
        $this->assertInstanceOf(PostgreSQLToMySQLDataSeeder::class, $seeder);
    }

    /**
     * Test that main DatabaseSeeder exists and can be instantiated.
     */
    public function test_database_seeder_exists(): void
    {
        $seeder = new DatabaseSeeder();
        $this->assertInstanceOf(DatabaseSeeder::class, $seeder);
    }

    /**
     * Test that seeder classes have required methods.
     */
    public function test_seeder_methods_exist(): void
    {
        $postgresSeeder = new PostgreSQLToMySQLDataSeeder();
        $this->assertTrue(method_exists($postgresSeeder, 'run'));

        $mainSeeder = new DatabaseSeeder();
        $this->assertTrue(method_exists($mainSeeder, 'run'));
    }
}
