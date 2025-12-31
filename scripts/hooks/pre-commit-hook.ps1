# Git Pre-commit Hook for ProjectTracker Fresh (PowerShell)
# This script runs essential tests before allowing a commit

Write-Host "Running Pre-commit Tests..." -ForegroundColor Cyan
Write-Host "================================"

# Run tests (prefer targeted tests if present, otherwise run the whole suite)
$testsToRun = @()

if (Test-Path "tests/Feature/ApplicationHealthTest.php") {
    $testsToRun += "tests/Feature/ApplicationHealthTest.php"
}

if (Test-Path "tests/Feature/Admin/HB837/InspectionCalendarTest.php") {
    $testsToRun += "tests/Feature/Admin/HB837/InspectionCalendarTest.php"
}

if ($testsToRun.Count -gt 0) {
    Write-Host "Running Selected Pre-commit Tests..." -ForegroundColor Yellow
    & php artisan test @testsToRun --stop-on-failure
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Pre-commit tests failed!" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "Running Test Suite (no targeted tests found)..." -ForegroundColor Yellow
    & php artisan test --stop-on-failure
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Pre-commit test suite failed!" -ForegroundColor Red
        exit 1
    }
}

# Run a quick syntax check
Write-Host "Running PHP Syntax Check..." -ForegroundColor Yellow
$phpFiles = Get-ChildItem -Recurse -Include "*.php" | Where-Object {
    $_.FullName -notlike "*vendor*" -and
    $_.FullName -notlike "*storage*" -and
    $_.FullName -notlike "*bootstrap\cache*"
}

$syntaxErrors = 0
foreach ($file in $phpFiles) {
    $result = & php -l $file.FullName 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Syntax error in: $($file.FullName)" -ForegroundColor Red
        Write-Host $result -ForegroundColor Red
        $syntaxErrors++
    }
}

if ($syntaxErrors -gt 0) {
    Write-Host "PHP Syntax errors found!" -ForegroundColor Red
    Write-Host "Please fix syntax errors before committing." -ForegroundColor Red
    exit 1
}

Write-Host "All pre-commit tests passed!" -ForegroundColor Green
Write-Host "Commit is allowed to proceed." -ForegroundColor Green
exit 0
