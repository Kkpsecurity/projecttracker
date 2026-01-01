# Debug Scripts

This directory contains debugging and troubleshooting scripts for various system components.

## Scripts

### Component Debugging
- **`debug_consultant.php`** - Debug consultant assignment issues
- **`debug_contracting_status.php`** - Debug contracting status problems
- **`debug_crime_risk.php`** - Debug crime risk field issues
- **`debug_form.php`** - Debug form submission and validation
- **`debug_process_consultant.php`** - Debug consultant processing logic

### UI Debugging
- **`debug_plots_ajax.php`** - Debug AJAX plot functionality
- **`test_hb837_import_debug.php`** - Debug HB837 import issues
- **`test_datatables_debug.php`** - Debug DataTables functionality

### System Debugging
- **`debug_test_sheets.php`** - Debug test sheet processing
- **`diagnostic_live_import.php`** - Live import diagnostics

## Usage

Run these scripts from the project root directory:

```bash
# Debug consultant assignment
php scripts/debug/debug_consultant.php

# Debug import issues
php scripts/debug/test_hb837_import_debug.php

# Run live import diagnostics
php scripts/debug/diagnostic_live_import.php
```

## Purpose

These scripts help with:
- Identifying system bugs and issues
- Troubleshooting import problems
- Debugging UI components
- Validating system functionality
