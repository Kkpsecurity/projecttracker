# Utility Scripts

This directory contains utility and maintenance scripts for system management.

## Data Management
- **`add_test_data.php`** - Add test data to the system
- **`add_test_record.php`** - Add individual test records
- **`clear_cache.php`** - Clear application cache
- **`clear_hb837_table.php`** - Clear HB837 table data

## Field Management
- **`check_billing_fields.php`** - Check billing field integrity
- **`check_columns.php`** - Check database column structure
- **`check_consultant_fields.php`** - Check consultant field mappings
- **`check_missing_field.php`** - Identify missing fields
- **`check_missing_fields_upload.php`** - Check missing fields in uploads
- **`check_tables.php`** - Check database table structure
- **`check_users.php`** - Check user data integrity

## Database Management
- **`create_missing_columns.php`** - Create missing database columns
- **`create_test_field.php`** - Create test fields for development

## System Utilities
- **`setup_mock_mode.bat`** - Setup mock mode for testing
- **`scroll_feature_summary.php`** - Scroll feature analysis
- **`upload_flow_changes.php`** - Upload flow modifications
- **`test_hb837_export.php`** - Test HB837 export functionality
- **`test_hb837_stats.php`** - Generate HB837 statistics

## Usage

Run utilities from the project root directory:

```bash
# Clear application cache
php scripts/util/clear_cache.php

# Check database structure
php scripts/util/check_tables.php

# Add test data
php scripts/util/add_test_data.php
```

## Purpose

These scripts provide:
- System maintenance capabilities
- Database management tools
- Data integrity checking
- Development utilities
- Export/import functionality
