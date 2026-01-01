# Import Scripts

This directory contains scripts for testing and validating the HB837 import functionality.

## Scripts

### Import Testing
- **`comprehensive_import_test.php`** - Complete import workflow testing
- **`test_comprehensive_import.php`** - Comprehensive import validation
- **`quick_import_test.php`** - Quick import functionality test
- **`live_import_test.php`** - Live import testing with real data

### Import Validation
- **`test_import_complete.php`** - Test complete import process
- **`test_contracting_import.php`** - Test contracting status imports
- **`test_full_import_sanitization.php`** - Test import data sanitization
- **`test_import_results_errors.php`** - Test import error handling

### Import Monitoring
- **`track_import_process.php`** - Track import process progress
- **`show_import_results.php`** - Display import results and statistics

## Usage

Run these scripts from the project root directory:

```bash
# Run comprehensive import test
php scripts/import/comprehensive_import_test.php

# Test with live data
php scripts/import/live_import_test.php

# Track import progress
php scripts/import/track_import_process.php
```

## Purpose

These scripts help with:
- Testing import functionality
- Validating data processing
- Monitoring import performance
- Debugging import issues
- Ensuring data integrity during imports
