# Test Scripts

This directory contains general testing scripts, test runners, and validation tools.

## Test Runners
- **`run-tests.ps1`** - PowerShell test runner
- **`run-tests.sh`** - Bash test runner
- **`run-tests-simple.ps1`** - Simple PowerShell test runner
- **`test-runner.ps1`** - Advanced test runner

## Component Tests
- **`test_ajax_response.php`** - Test AJAX responses
- **`test_assigned_consultant.php`** - Test consultant assignment
- **`test_comparison_view.php`** - Test comparison view functionality
- **`test_dashboard_*.php`** - Dashboard testing scripts
- **`test_datatables_fix.php`** - DataTables functionality tests

## Field Testing
- **`test_field_*.php`** - Field validation and mapping tests
- **`test_enhanced_mappings.php`** - Enhanced field mapping tests
- **`test_mapping.php`** - General mapping tests
- **`test_mapped_fields.php`** - Mapped field validation

## UI Testing
- **`test_consultant_*.php`** - Consultant UI testing scripts
- **`test_mock_datatables.php`** - Mock DataTables testing
- **`test_pdf_functionality.php`** - PDF generation testing
- **`test_plots_ajax.php`** - AJAX plots testing

## Data Testing
- **`test_property_type_normalization.php`** - Property type validation
- **`test_regional_manager.php`** - Regional manager testing
- **`test_report_status.php`** - Report status testing

## Usage

Run tests from the project root directory:

```bash
# Run all tests
./scripts/test/run-tests.sh

# Run specific test
php scripts/test/test_dashboard_live.php

# Run PowerShell test suite
./scripts/test/run-tests.ps1
```

## Purpose

These scripts provide:
- Automated testing capabilities
- Component validation
- UI functionality testing
- Data integrity verification
- Regression testing
