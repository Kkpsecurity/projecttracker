# 🔧 Scripts Directory

This directory contains all utility scripts, test files, and workflow tools for the KKP Security Project Tracker.

## 📂 Directory Structure

### 🧪 **Test Scripts**
Files prefixed with `test_` - These are debugging and testing utilities:

- `test_ajax_response.php` - Test AJAX endpoint responses
- `test_consultant_validation.php` - Test consultant validation logic
- `test_dashboard_live.php` - Test live dashboard functionality
- `test_dashboard_stats.php` - Test dashboard statistics
- `test_datatables_debug.php` - Debug DataTables issues
- `test_datatables_fix.php` - DataTables fix validation
- `test_field_mapping_config.php` - Test field mapping configuration
- `test_fixes.php` - General fixes testing
- `test_full_import_sanitization.php` - Test import data sanitization
- `test_hb837_export.php` - Test HB837 export functionality
- `test_hb837_stats.php` - Test HB837 statistics
- `test_import_results_errors.php` - Test import error handling
- `test_mapped_fields.php` - Test field mapping
- `test_mock_datatables.php` - Mock DataTables testing
- `test_plots_ajax.php` - Test plots AJAX functionality
- `test_property_type_normalization.php` - Test property type normalization

### 📊 **Excel Analysis Scripts**
Scripts for analyzing and debugging Excel imports:

- `analyze_all_excel.php` - Comprehensive Excel file analysis
- `analyze_excel_headers.php` - Excel header analysis
- `analyze_excel_simple.php` - Simple Excel analysis
- `analyze_excel_structure.php` - Excel structure analysis

### 🔧 **Utility Scripts**
General purpose utility scripts:

- `check_missing_field.php` - Check for missing database fields
- `check_missing_fields_upload.php` - Check missing fields in uploads
- `clear_cache.php` - Clear application cache
- `create_missing_columns.php` - Create missing database columns
- `create_test_field.php` - Create test fields
- `debug_plots_ajax.php` - Debug plots AJAX calls
- `diagnostic_live_import.php` - Live import diagnostics
- `scroll_feature_summary.php` - Scroll feature analysis
- `upload_flow_changes.php` - Upload flow modifications

### 🔄 **Workflow Scripts**
Business process and workflow scripts:

- `workflow_master.php` - Master workflow controller
- `workflow_step_1_quotation.php` - Quotation workflow step
- `workflow_step_2_execution.php` - Execution workflow step
- `workflow_step_3_completion.php` - Completion workflow step

### 🧪 **Test Runners**
Scripts for running tests and validation:

- `run-tests-simple.ps1` - Simple PowerShell test runner
- `run-tests.ps1` - Full PowerShell test suite
- `run-tests.sh` - Bash test runner for Linux/Mac
- `test-runner.ps1` - Advanced test runner
- `setup_mock_mode.bat` - Setup mock mode for testing

### 🔗 **Git Hooks**
Git automation scripts:

- `pre-commit-hook.ps1` - PowerShell pre-commit hook
- `pre-commit-hook.sh` - Bash pre-commit hook

## 🚀 **Usage Instructions**

### Running Test Scripts
```bash
# Run individual test
php scripts/test_dashboard_live.php

# Run test suite
./scripts/run-tests.sh
```

### Excel Analysis
```bash
# Analyze an Excel file
php scripts/analyze_all_excel.php /path/to/file.xlsx
```

### Workflow Scripts
```bash
# Execute workflow step
php scripts/workflow_step_1_quotation.php
```

### Utility Scripts
```bash
# Clear cache
php scripts/clear_cache.php

# Check missing fields
php scripts/check_missing_field.php
```

## ⚠️ **Important Notes**

1. **Environment**: These scripts should be run from the project root directory
2. **Permissions**: Ensure proper file permissions for shell scripts
3. **Database**: Some scripts require database access
4. **Testing**: Test scripts are for development/debugging only

## 📝 **Adding New Scripts**

When adding new scripts to this directory:

1. Use descriptive names with appropriate prefixes (`test_`, `analyze_`, `workflow_`)
2. Add documentation at the top of each file
3. Update this README.md file
4. Test thoroughly before committing

## 🔧 **Maintenance**

Regular maintenance tasks:
- Review and clean up outdated test scripts
- Update documentation as scripts evolve
- Archive or remove unused scripts

---

**Last Updated**: July 4, 2025  
**Project**: KKP Security Project Tracker  
**Version**: 1.0
