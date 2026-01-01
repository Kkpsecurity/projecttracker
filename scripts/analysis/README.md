# Analysis Scripts

This directory contains scripts for analyzing Excel files, test sheets, and data structures related to the HB837 import functionality.

## Scripts

### Excel Analysis
- **`analyze_all_excel.php`** - Comprehensive Excel file analysis
- **`analyze_excel_headers.php`** - Excel header analysis and mapping
- **`analyze_excel_simple.php`** - Simple Excel structure analysis
- **`analyze_excel_structure.php`** - Detailed Excel structure analysis

### Test Sheet Analysis
- **`analyze_all_test_sheets.php`** - Analysis of all test sheets
- **`test_sheet_analysis.php`** - Individual test sheet analysis

### Data Analysis
- **`analyze_consultant_lookup.php`** - Consultant lookup and mapping analysis
- **`analyze_import_logs.php`** - Import log analysis and debugging

## Usage

Run these scripts from the project root directory:

```bash
# Analyze a specific Excel file
php scripts/analysis/analyze_excel_structure.php /path/to/file.xlsx

# Analyze all test sheets
php scripts/analysis/analyze_all_test_sheets.php

# Check consultant lookup functionality
php scripts/analysis/analyze_consultant_lookup.php
```

## Purpose

These scripts help with:
- Understanding Excel file structures before import
- Validating field mappings
- Debugging import issues
- Analyzing test data quality
