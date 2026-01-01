Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   PROJECTTRACKER FRESH TEST SUITE" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Set error preference
$ErrorActionPreference = "Continue"

# Test counters
$totalTests = 0
$passedTests = 0
$failedTests = 0

function Run-TestSuite {
    param(
        [string]$TestName,
        [string]$TestCommand
    )

    Write-Host "Testing $TestName..." -ForegroundColor Yellow
    $totalTests++

    try {
        Invoke-Expression $TestCommand | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  ✓ $TestName" -ForegroundColor Green
            $script:passedTests++
        } else {
            Write-Host "  ✗ $TestName" -ForegroundColor Red
            $script:failedTests++
        }
    } catch {
        Write-Host "  ✗ $TestName (Error: $($_.Exception.Message))" -ForegroundColor Red
        $script:failedTests++
    }

    Write-Host ""
}

# Application Health Tests
Write-Host "Application Health Tests" -ForegroundColor Cyan
Write-Host "------------------------" -ForegroundColor Cyan
Run-TestSuite "Database Connection" "php artisan test --filter='database_connection_is_working'"
Run-TestSuite "Critical Routes" "php artisan test --filter='critical_routes_are_accessible'"
Run-TestSuite "HB837 CRUD Operations" "php artisan test --filter='hb837_crud_operations_work'"
Run-TestSuite "AJAX Endpoints" "php artisan test --filter='ajax_endpoints_respond_correctly'"
Run-TestSuite "Import Configuration" "php artisan test --filter='import_field_configuration_system_works'"

# Inspection Calendar Tests
Write-Host "Inspection Calendar Tests" -ForegroundColor Cyan
Write-Host "-------------------------" -ForegroundColor Cyan
Run-TestSuite "Calendar Page Display" "php artisan test --filter='it_can_display_inspection_calendar_page'"
Run-TestSuite "Calendar Events API" "php artisan test --filter='it_can_fetch_calendar_events_with_scheduled_inspections'"
Run-TestSuite "Status Filtering" "php artisan test --filter='it_can_filter_events_by_status'"
Run-TestSuite "Project Details" "php artisan test --filter='it_can_fetch_project_details'"
Run-TestSuite "Date Updates" "php artisan test --filter='it_can_update_inspection_date'"

# Final Results
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "           TEST RESULTS SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

if ($failedTests -eq 0) {
    Write-Host "ALL TESTS PASSED! ($passedTests passed, $failedTests failed out of $totalTests)" -ForegroundColor Green
} else {
    Write-Host "SOME TESTS FAILED! ($passedTests passed, $failedTests failed out of $totalTests)" -ForegroundColor Red
    Write-Host "Please fix the failing tests before proceeding." -ForegroundColor Red
}

Write-Host ""
Write-Host "Tip: Use 'php artisan test' for full test suite" -ForegroundColor Gray
Write-Host "Test completed at $(Get-Date -Format 'MM/dd/yyyy HH:mm:ss')" -ForegroundColor Gray

if ($failedTests -eq 0) {
    exit 0
} else {
    exit 1
}
