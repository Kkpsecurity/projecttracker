# PostgreSQL to MySQL Migration Plan

**Current State**: Laravel 11.45.1 with PostgreSQL database  
**Target**: MySQL 8.0+ database with full data migration  
**Priority**: Critical - Required before AdminLTE integration  
**Date**: June 26, 2025

## 🎯 Migration Overview

### Current Database Status
- **Source**: PostgreSQL 9.6.22 (from dump file)
- **Target**: MySQL 8.0+ (Laragon environment)
- **Laravel Config**: Already configured for MySQL ✅
- **Environment**: MySQL connection ready in .env ✅

### Data Analysis from PostgreSQL Dump
Based on the provided dump file, the database contains:

#### Core Tables Identified:
1. **backups** - Database backup management (11 records)
2. **clients** - Main project clients (48+ records)
3. **consultants** - Consultant management
4. **hb837** - HB837 project records
5. **plot_addresses** - Geographic plot data
6. **import_audits** - Data import tracking
7. **users** - User management
8. **owners** - Property owners
9. **plots** - Plot/property data

#### Key Data Characteristics:
- **Complex relationships** between clients, plots, and consultants
- **File attachments** stored as references
- **Status tracking** with multiple states
- **Financial data** (services totals, billing amounts)
- **Geographic data** for mapping functionality
- **Audit trails** for imports and changes

## 📋 Migration Strategy

### Phase 1: Environment Preparation (1-2 hours)

#### Step 1: MySQL Database Setup
```bash
# Create MySQL database in Laragon
# Access phpMyAdmin or MySQL command line:
CREATE DATABASE projecttracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Step 2: Laravel Migration Validation
```bash
# Verify current migration status
php artisan migrate:status

# Create fresh migration state for MySQL
php artisan migrate:fresh --seed
```

#### Step 3: Backup Current State
```bash
# Create git backup point
git add .
git commit -m "🔄 Pre-MySQL migration backup"
git tag pre-mysql-migration
```

### Phase 2: Data Conversion (3-4 hours)

#### Step 1: PostgreSQL to MySQL Schema Conversion

**Key Differences to Address:**
- **Boolean fields**: PostgreSQL `boolean` → MySQL `TINYINT(1)`
- **Text fields**: PostgreSQL `text` → MySQL `TEXT` or `LONGTEXT`
- **Timestamps**: PostgreSQL specific formats → MySQL DATETIME
- **Sequences**: PostgreSQL sequences → MySQL AUTO_INCREMENT
- **JSON fields**: Ensure proper MySQL JSON type usage

#### Step 2: Data Export from PostgreSQL
```bash
# Option 1: Use pgAdmin or pg_dump for clean export
pg_dump -U projecttracker -h localhost -d projecttracker --data-only --inserts > data_export.sql

# Option 2: Use Laravel database tools (if PostgreSQL still accessible)
php artisan db:seed --class=PostgreSQLDataExportSeeder
```

#### Step 3: Data Import to MySQL
```bash
# Import data using Laravel seeders (recommended)
php artisan db:seed --class=MySQLDataImportSeeder

# Or direct MySQL import (after conversion)
mysql -u root -p projecttracker < converted_data.sql
```

### Phase 3: Data Transformation & Validation (2-3 hours)

#### Step 1: Create Conversion Seeders
Based on the PostgreSQL dump, create Laravel seeders for:

1. **ClientsSeeder** - Import all 48+ client records
2. **ConsultantsSeeder** - Import consultant data
3. **HB837Seeder** - Import HB837 project data
4. **PlotsSeeder** - Import plot/address data
5. **BackupsSeeder** - Import backup records
6. **UsersSeeder** - Import user accounts

#### Step 2: Data Integrity Validation
```bash
# Verify record counts match
php artisan tinker
> App\Models\Client::count(); // Should match PostgreSQL count
> App\Models\HB837::count();
> App\Models\Consultant::count();
```

#### Step 3: Relationship Validation
```bash
# Test key relationships
php artisan tinker
> $client = App\Models\Client::first();
> $client->projects; // Test relationships
> $client->consultants;
```

## 🔧 Technical Implementation

### Migration Script Structure
```php
<?php
// database/seeders/PostgreSQLToMySQLSeeder.php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostgreSQLToMySQLSeeder extends Seeder
{
    public function run()
    {
        // Import clients data
        $this->importClients();
        
        // Import consultants data
        $this->importConsultants();
        
        // Import HB837 data
        $this->importHB837();
        
        // Import plots and addresses
        $this->importPlots();
        
        // Import backup records
        $this->importBackups();
        
        // Validate data integrity
        $this->validateImport();
    }
    
    private function importClients()
    {
        // Client data from PostgreSQL dump
        $clients = [
            [
                'id' => 1,
                'client_name' => 'Kohl\'s',
                'project_name' => 'Active Shooter Assessment of Kohl\'s Facilities',
                'poc' => 'N/A - All is going right now through Kohl\'s procurement hub.',
                'status' => 'Received word they did not want to proceed with our participation further in the RFP.',
                'quick_status' => 'Closed',
                'description' => 'Active shooter assessment of four Kohl\'s facilities including corporate headquarters, another office complex, one distribution center, and one retail center.',
                'corporate_name' => 'CIS',
                'created_at' => '2024-12-18 19:07:50',
                'updated_at' => '2024-12-18 19:07:50',
            ],
            // ... continue for all 48+ records
        ];
        
        DB::table('clients')->insert($clients);
    }
    
    // Additional import methods...
}
```

### Data Type Conversions
```sql
-- PostgreSQL to MySQL field mappings
-- Boolean fields
ALTER TABLE clients MODIFY COLUMN active TINYINT(1) DEFAULT 0;

-- Text fields with proper encoding
ALTER TABLE clients 
MODIFY COLUMN description LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Ensure proper timestamp handling
ALTER TABLE clients 
MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
```

## 📊 Data Migration Checklist

### Pre-Migration Validation ✅
- [ ] MySQL database created and accessible
- [ ] Laravel migrations run successfully
- [ ] Current PostgreSQL data backed up
- [ ] Data transformation scripts prepared

### Migration Execution ✅
- [ ] **Clients table**: 48+ records migrated
- [ ] **Consultants table**: All consultant data migrated  
- [ ] **HB837 table**: All project records migrated
- [ ] **Plots/Addresses**: Geographic data migrated
- [ ] **Backups table**: Backup history migrated
- [ ] **Users table**: User accounts migrated

### Post-Migration Validation ✅
- [ ] **Record counts match** PostgreSQL source
- [ ] **Relationships intact** between all entities
- [ ] **Data integrity verified** (no corruption)
- [ ] **Application functionality** fully working
- [ ] **File attachments** accessible and working
- [ ] **Search functionality** operational

## 🔍 Specific Data Transformations

### Client Data (48+ Records)
Key fields requiring attention:
- **Status handling**: Multiple status types (Active, Closed, New Lead, etc.)
- **Financial data**: Services totals, billing amounts (NULL handling)
- **Text fields**: Long descriptions with special characters
- **File references**: file1, file2, file3 columns

### HB837 Project Data
- **Complex project structures**
- **Multi-step workflow status**
- **Associated consultant relationships**
- **Geographic plot associations**

### Geographic Data (Plots/Addresses)
- **Coordinate data** for Google Maps integration
- **Address validation** and formatting
- **Plot-to-project relationships**

### Backup System Data
- **UUID fields** for backup identification
- **File size and record counts**
- **Status tracking** (completed, failed, etc.)

## 🧪 Testing Strategy

### Functional Testing
```bash
# Test core application features
php artisan test

# Test database connections
php artisan migrate:status
php artisan db:seed --class=TestDataSeeder

# Test web interface
# Navigate to each major section and verify data display
```

### Data Integrity Testing
```php
// Custom validation script
<?php
// tests/Feature/DatabaseMigrationTest.php

class DatabaseMigrationTest extends TestCase
{
    public function test_client_data_integrity()
    {
        $this->assertEquals(48, Client::count());
        
        $kohls = Client::where('client_name', 'Kohl\'s')->first();
        $this->assertNotNull($kohls);
        $this->assertEquals('CIS', $kohls->corporate_name);
    }
    
    public function test_relationships_intact()
    {
        // Test client-consultant relationships
        // Test plot-address relationships
        // Test backup-user relationships
    }
}
```

## 📅 Migration Timeline

### Day 1: Preparation & Setup (2-3 hours)
- **Hour 1**: MySQL database setup and Laravel configuration
- **Hour 2**: Data analysis and conversion script preparation
- **Hour 3**: Testing environment setup and validation

### Day 2: Data Migration (4-6 hours)
- **Hours 1-2**: Execute data import scripts
- **Hours 3-4**: Data validation and integrity checks
- **Hours 5-6**: Application testing and bug fixes

### Day 3: Validation & Optimization (2-3 hours)
- **Hour 1**: Comprehensive application testing
- **Hour 2**: Performance optimization
- **Hour 3**: Documentation and rollback plan validation

## 🚨 Risk Assessment

### High Risk Areas
1. **Data Loss**: Complex relationships might break during conversion
2. **Character Encoding**: Special characters in text fields
3. **File References**: Attached file paths and accessibility
4. **Performance**: MySQL performance vs PostgreSQL

### Mitigation Strategies
1. **Multiple Backups**: Git, database dumps, file system backups
2. **Incremental Testing**: Validate each table separately
3. **Rollback Plan**: Quick restoration to PostgreSQL if needed
4. **Data Verification**: Automated scripts to compare before/after

## 🔄 Rollback Plan

### Immediate Rollback (30 minutes)
1. **Restore .env** to PostgreSQL settings
2. **Restore database** from PostgreSQL backup
3. **Verify functionality** with original data

### Complete Environment Restoration (1-2 hours)
1. **Full git reset** to pre-migration state
2. **Database restoration** from multiple backup points
3. **File system restoration** if needed
4. **Complete application testing**

## ✅ Success Criteria

### Technical Success
- [ ] All PostgreSQL data successfully migrated to MySQL
- [ ] Zero data loss or corruption
- [ ] All application features working identically
- [ ] Performance maintained or improved
- [ ] Laravel 11 fully compatible with new database

### Business Success
- [ ] All client projects accessible and accurate
- [ ] HB837 workflow functioning correctly
- [ ] Mapping and geographic features working
- [ ] Backup system operational
- [ ] User authentication and permissions intact

## 🚀 Next Steps After Migration

### Immediate Tasks (Same Day)
1. **Performance optimization** for MySQL
2. **Index optimization** for better query performance
3. **Connection pooling** configuration
4. **Backup system** reconfiguration for MySQL

### Follow-up Tasks (Next Week)
1. **AdminLTE integration** (previously planned)
2. **Database monitoring** setup
3. **Performance benchmarking**
4. **Documentation updates**

---

**Ready to execute PostgreSQL to MySQL migration? This will provide a more standard, performant database foundation for the AdminLTE integration that follows.**
