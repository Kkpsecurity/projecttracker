# Workflow Scripts

This directory contains business process workflow scripts for the HB837 system.

## Scripts

### Master Workflow
- **`workflow_master.php`** - Master workflow controller and coordinator

### Workflow Steps
- **`workflow_step_1_quotation.php`** - Step 1: Initial quotation workflow
- **`workflow_step_2_execution.php`** - Step 2: Contract execution workflow  
- **`workflow_step_3_completion.php`** - Step 3: Project completion workflow

### Workflow Testing
- **`simple_contract_test.php`** - Simple contract workflow testing
- **`report_status_test.php`** - Report status progression testing

## Workflow Overview

The HB837 system follows a 3-step business workflow:

1. **Quotation Phase** - Initial property assessment and pricing
2. **Execution Phase** - Contract execution and project initiation
3. **Completion Phase** - Project completion and final reporting

## Usage

Run workflow scripts from the project root directory:

```bash
# Run complete workflow
php scripts/workflow/workflow_master.php

# Run specific workflow step
php scripts/workflow/workflow_step_1_quotation.php

# Test workflow functionality
php scripts/workflow/simple_contract_test.php
```

## Purpose

These scripts provide:
- Business process automation
- Workflow step coordination
- Status progression management
- Workflow testing and validation
- Process monitoring and tracking
