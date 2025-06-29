<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerateSchemaDoc extends Command
{
    protected $signature = 'schema:generate-doc';
    protected $description = 'Generate database schema documentation';

    public function handle()
    {
        $this->info('=== DATABASE SCHEMA DOCUMENTATION GENERATOR ===');
        
        try {
            // Get all tables
            $tables = DB::select("SHOW TABLES");
            $tableKey = array_keys((array)$tables[0])[0];
            
            $this->info('Found ' . count($tables) . ' tables:');
            
            $schemaData = [];
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                $this->line("Analyzing table: $tableName");
                
                // Get columns
                $columns = Schema::getColumnListing($tableName);
                $columnTypes = [];
                
                foreach ($columns as $column) {
                    $columnTypes[$column] = Schema::getColumnType($tableName, $column);
                }
                
                // Get row count
                try {
                    $rowCount = DB::table($tableName)->count();
                } catch (\Exception $e) {
                    $rowCount = 0;
                }
                
                $schemaData[$tableName] = [
                    'columns' => $columns,
                    'column_types' => $columnTypes,
                    'row_count' => $rowCount
                ];
            }
            
            // Generate documentation
            $markdown = $this->generateMarkdownDocumentation($schemaData);
            
            // Ensure directory exists
            if (!is_dir(base_path('docs/architecture'))) {
                mkdir(base_path('docs/architecture'), 0755, true);
            }
            
            // Save file
            file_put_contents(base_path('docs/architecture/database-schema.md'), $markdown);
            
            $this->info('Schema documentation generated successfully!');
            $this->info('Saved to: docs/architecture/database-schema.md');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
    
    private function generateMarkdownDocumentation($schemaData)
    {
        $markdown = "# Database Schema Documentation\n\n";
        $markdown .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        $markdown .= "## Overview\n\n";
        $markdown .= "This document provides a comprehensive overview of the ProjectTracker database schema, including all tables, columns, relationships, and constraints.\n\n";
        
        // Table of Contents
        $markdown .= "## Table of Contents\n\n";
        foreach ($schemaData as $tableName => $data) {
            $markdown .= "- [" . ucfirst(str_replace('_', ' ', $tableName)) . "](#" . str_replace('_', '-', $tableName) . ")\n";
        }
        $markdown .= "\n";
        
        // Database Statistics
        $markdown .= "## Database Statistics\n\n";
        $markdown .= "| Table | Row Count |\n";
        $markdown .= "|-------|----------|\n";
        foreach ($schemaData as $tableName => $data) {
            $markdown .= "| `$tableName` | " . number_format($data['row_count']) . " |\n";
        }
        $markdown .= "\n";
        
        // Entity Relationship Overview
        $markdown .= "## Entity Relationship Overview\n\n";
        $markdown .= "The database follows a normalized structure with the following key relationships:\n\n";
        $markdown .= "- **Users** can have administrative privileges and track login activity\n";
        $markdown .= "- **Clients** represent companies with billing information and contact details\n";
        $markdown .= "- **Consultants** are specialized staff members who handle HB837 applications\n";
        $markdown .= "- **HB837** applications are linked to consultants and plots\n";
        $markdown .= "- **Plots** have associated addresses and can be referenced by multiple applications\n";
        $markdown .= "- **Import Audits** track data import operations across all tables\n";
        $markdown .= "- **Backups** maintain records of database backup operations\n";
        $markdown .= "- **Site Settings** store application configuration values\n\n";
        
        // Detailed table documentation
        foreach ($schemaData as $tableName => $data) {
            $markdown .= "## " . ucfirst(str_replace('_', ' ', $tableName)) . "\n\n";
            $markdown .= "**Table Name:** `$tableName`\n";
            $markdown .= "**Row Count:** " . number_format($data['row_count']) . "\n\n";
            
            // Add purpose description
            $purpose = $this->getTablePurpose($tableName);
            if ($purpose) {
                $markdown .= "**Purpose:** $purpose\n\n";
            }
            
            // Columns
            $markdown .= "### Columns\n\n";
            $markdown .= "| Column | Type | Description |\n";
            $markdown .= "|--------|------|-------------|\n";
            
            foreach ($data['columns'] as $column) {
                $type = $data['column_types'][$column] ?? 'unknown';
                $description = $this->getColumnDescription($tableName, $column);
                
                $markdown .= "| `$column` | $type | $description |\n";
            }
            $markdown .= "\n";
            
            $markdown .= "---\n\n";
        }
        
        return $markdown;
    }
    
    private function getTablePurpose($tableName)
    {
        $purposes = [
            'users' => 'Manages user accounts, authentication, and administrative access',
            'password_resets' => 'Handles password reset tokens for user authentication',
            'failed_jobs' => 'Tracks failed background job executions for debugging',
            'client' => 'Stores client company information, contact details, and billing rates',
            'consultants' => 'Manages consultant profiles, specializations, and employment status',
            'hb837' => 'Tracks HB837 applications, their status, and consultant assignments',
            'plots' => 'Manages plot information and identifiers',
            'plot_addresses' => 'Stores detailed address information for plots',
            'import_audits' => 'Logs all data import operations and their results',
            'backups' => 'Records database backup operations and file locations',
            'site_settings' => 'Stores application-wide configuration settings',
            'migrations' => 'Laravel framework table tracking applied database migrations'
        ];
        
        return $purposes[$tableName] ?? null;
    }
    
    private function getColumnDescription($tableName, $columnName)
    {
        $descriptions = [
            // Common columns
            'id' => 'Primary key identifier',
            'created_at' => 'Record creation timestamp',
            'updated_at' => 'Record last update timestamp',
            'deleted_at' => 'Soft deletion timestamp',
            'email' => 'Email address',
            'password' => 'Encrypted password',
            'name' => 'Name field',
            'status' => 'Record status',
            
            // Users table
            'email_verified_at' => 'Email verification timestamp',
            'remember_token' => 'Remember me token for login persistence',
            'is_admin' => 'Administrative privileges flag',
            'last_login_at' => 'Last login timestamp',
            'login_count' => 'Number of times user has logged in',
            
            // Client table
            'company_name' => 'Client company name',
            'contact_person' => 'Primary contact person',
            'phone' => 'Contact phone number',
            'address' => 'Physical address',
            'logo' => 'Company logo file path',
            'rate_per_hour' => 'Hourly billing rate',
            'currency' => 'Currency for billing',
            
            // HB837 table
            'consultant_id' => 'Reference to consultant handling the case',
            'plot_number' => 'Plot identification number',
            'current_status' => 'Current processing status',
            'consultant_notes' => 'Notes from assigned consultant',
            'date_received' => 'Date application was received',
            'date_completed' => 'Date application was completed',
            
            // Consultants table
            'specialization' => 'Area of expertise',
            'hire_date' => 'Date consultant was hired',
            'is_active' => 'Active employment status',
            
            // Plot addresses
            'plot_id' => 'Reference to associated plot',
            'street_address' => 'Street address of the plot',
            'city' => 'City location',
            'state' => 'State/province',
            'postal_code' => 'Postal/ZIP code',
            'country' => 'Country',
            
            // Import audits
            'table_name' => 'Name of table being imported to',
            'file_name' => 'Original import file name',
            'records_imported' => 'Number of records successfully imported',
            'records_failed' => 'Number of records that failed import',
            'import_status' => 'Overall import operation status',
            'error_details' => 'Details of any import errors',
            
            // Backups
            'backup_path' => 'File system path to backup file',
            'backup_size' => 'Size of backup file in bytes',
            'backup_type' => 'Type of backup (full, incremental, etc.)',
            'backup_status' => 'Backup operation status',
            
            // Site settings
            'key' => 'Setting identifier key',
            'value' => 'Setting value',
            'description' => 'Human-readable setting description',
        ];
        
        return $descriptions[$columnName] ?? 'Field description';
    }
}
