# Simple PowerShell Test Runner for Laravel Application
param(
    [switch]$Quick,
    [switch]$Help
)

if ($Help) {
    Write-Host "Usage: .\run-tests.ps1 [-Quick] [-Help]"
    Write-Host ""
    Write-Host "Options:"
    Write-Host "  -Quick  - Run only critical tests"
    Write-Host "  -Help   - Show this help"
    exit 0
}

# Check if we're in a Laravel project
if (-not (Test-Path "artisan")) {
    Write-Host "Error: Not in a Laravel project directory" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Blue
Write-Host "LARAVEL APPLICATION TEST SUITE" -ForegroundColor Blue
Write-Host "================================================" -ForegroundColor Blue
Write-Host ""

# Initialize counters
$TotalTests = 0
$PassedTests = 0
$FailedTests = 0

Write-Host "Pre-test checks..." -ForegroundColor Cyan

# Clear caches
php artisan config:clear *>$null
php artisan route:clear *>$null
php artisan view:clear *>$null
Write-Host "Cache cleared" -ForegroundColor Green

# Test database connection
$dbTest = php artisan migrate:status *>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "Database connection OK" -ForegroundColor Green
} else {
    Write-Host "Database connection failed" -ForegroundColor Red
}

Write-Host ""
if ($Quick) {
    Write-Host "Running Critical Tests..." -ForegroundColor Yellow
} else {
    Write-Host "Running Full Test Suite..." -ForegroundColor Yellow
}
Write-Host ""

# Run Application Health Tests
Write-Host "Testing Application Health..." -ForegroundColor Cyan
php artisan test tests/Feature/ApplicationHealthTest.php --stop-on-failure
if ($LASTEXITCODE -eq 0) {
    Write-Host "Application Health Tests passed!" -ForegroundColor Green
    $PassedTests++
} else {
    Write-Host "Application Health Tests failed!" -ForegroundColor Red
    $FailedTests++
}
$TotalTests++

# Run Inspection Calendar Tests
Write-Host ""
Write-Host "Testing Inspection Calendar..." -ForegroundColor Cyan
php artisan test tests/Feature/Admin/HB837/InspectionCalendarTest.php --stop-on-failure
if ($LASTEXITCODE -eq 0) {
    Write-Host "Inspection Calendar Tests passed!" -ForegroundColor Green
    $PassedTests++
} else {
    Write-Host "Inspection Calendar Tests failed!" -ForegroundColor Red
    $FailedTests++
}
$TotalTests++

if (-not $Quick) {
    # Run additional tests if they exist
    if (Test-Path "tests/Feature/HB837ControllerTest.php") {
        Write-Host ""
        Write-Host "Testing HB837 Controller..." -ForegroundColor Cyan
        php artisan test tests/Feature/HB837ControllerTest.php --stop-on-failure
        if ($LASTEXITCODE -eq 0) {
            Write-Host "HB837 Controller Tests passed!" -ForegroundColor Green
            $PassedTests++
        } else {
            Write-Host "HB837 Controller Tests failed!" -ForegroundColor Red
            $FailedTests++
        }
        $TotalTests++
    }
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Blue
Write-Host "TEST RESULTS SUMMARY" -ForegroundColor Blue
Write-Host "================================================" -ForegroundColor Blue

if ($FailedTests -eq 0) {
    Write-Host "ALL TESTS PASSED! ($PassedTests/$TotalTests)" -ForegroundColor Green
    Write-Host "Application is ready for deployment!" -ForegroundColor Green
    $ExitCode = 0
} else {
    Write-Host "SOME TESTS FAILED! ($PassedTests passed, $FailedTests failed out of $TotalTests)" -ForegroundColor Red
    Write-Host "Please fix the failing tests before proceeding." -ForegroundColor Yellow
    $ExitCode = 1
}

Write-Host ""
Write-Host "Tip: Use 'php artisan test' for full test suite" -ForegroundColor Cyan
Write-Host "Test completed at $(Get-Date)" -ForegroundColor Blue

exit $ExitCode
