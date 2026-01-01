# 🔧 Scripts Directory

This directory contains all utility scripts, test files, and workflow tools for the KKP Security Project Tracker.

## 📂 Directory Structure

### 📊 **analysis/** - Excel and Data Analysis Scripts (8 scripts)
Scripts for analyzing Excel files, test sheets, and data structures:

- Excel structure and header analysis
- Test sheet validation and mapping
- Consultant lookup analysis
- Import log analysis and debugging

### 🐛 **debug/** - Debugging and Troubleshooting Tools (10 scripts)
Scripts for debugging system components and issues:

- Component debugging (consultant, contracting status, crime risk)
- UI debugging (forms, AJAX, DataTables)
- Import debugging and diagnostics
- Live system troubleshooting

### 📥 **import/** - Import Testing and Validation Scripts (10 scripts)
Scripts for testing and validating HB837 import functionality:

- Comprehensive import testing
- Live import validation
- Import error handling and monitoring
- Data sanitization and integrity checks

### 🧪 **test/** - General Testing Scripts and Test Runners (33 scripts)
Comprehensive testing suite including:

- Test runners (PowerShell and Bash)
- Component testing (AJAX, dashboard, DataTables)
- Field validation and mapping tests
- UI functionality testing

### 🔧 **util/** - Utility and Maintenance Scripts (18 scripts)
System management and maintenance tools:

- Data management (add/clear test data)
- Database management (check tables, create columns)
- Field integrity checking
- Export/import utilities

### 🔄 **workflow/** - Business Workflow Scripts (6 scripts)
Business process and workflow automation:

- Master workflow controller and step-by-step execution
- 3-phase business process (quotation → execution → completion)
- Workflow testing and validation
- Status progression management

### � **hooks/** - Git Hooks and Automation (2 scripts)
Git automation scripts:

- Pre-commit hooks for code quality
- Cross-platform hook support (PowerShell and Bash)

### 📁 **archive/** - Archived and Obsolete Scripts
Repository for outdated or unused scripts that may need future reference.

---

## � **Usage Instructions**

### Running Analysis Scripts
```bash
# Analyze Excel file structure
php scripts/analysis/analyze_excel_structure.php /path/to/file.xlsx

# Analyze all test sheets
php scripts/analysis/analyze_all_test_sheets.php
```

### Running Tests
```bash
# Run full test suite
./scripts/test/run-tests.sh

# Run specific test
php scripts/test/test_dashboard_live.php
```

### Import Testing
```bash
# Run comprehensive import test
php scripts/import/comprehensive_import_test.php

# Test with live data
php scripts/import/live_import_test.php
```

### Using Utilities
```bash
# Clear cache
php scripts/util/clear_cache.php

# Check database structure
php scripts/util/check_tables.php
```

### Workflow Execution
```bash
# Run complete workflow
php scripts/workflow/workflow_master.php

# Execute specific workflow step
php scripts/workflow/workflow_step_1_quotation.php
```

### Debugging Issues
```bash
# Debug consultant assignment
php scripts/debug/debug_consultant.php

# Debug import issues
php scripts/debug/test_hb837_import_debug.php
```

## ⚠️ **Important Notes**

1. **Environment**: These scripts should be run from the project root directory
2. **Permissions**: Ensure proper file permissions for shell scripts
3. **Database**: Some scripts require database access
4. **Testing**: Test scripts are for development/debugging only

## 📝 **Adding New Scripts**

When adding new scripts to this directory:

1. **Choose the correct directory** based on script purpose:
   - `analysis/` - Excel/data analysis tools
   - `debug/` - Debugging and troubleshooting
   - `import/` - Import testing and validation
   - `test/` - General testing and test runners
   - `util/` - Utilities and maintenance
   - `workflow/` - Business process scripts
   - `hooks/` - Git automation

2. **Use descriptive names** with appropriate prefixes when helpful
3. **Add documentation** at the top of each file
4. **Update the relevant directory README.md** file
5. **Test thoroughly** before committing

## 🔧 **Maintenance**

Regular maintenance tasks:
- Review and clean up outdated scripts (move to `archive/`)
- Update documentation as scripts evolve
- Ensure directory organization remains logical
- Keep README files current with script purposes

---

**Last Updated**: July 15, 2025  
**Project**: KKP Security Project Tracker  
**Version**: 2.0 - Organized Structure  
**Total Scripts**: 87 scripts organized into 7 functional directories
