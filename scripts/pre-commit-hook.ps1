# Git Pre-commit Hook for ProjectTracker Fresh (PowerShell)
# This script runs essential tests before allowing a commit

Write-Host "Running Pre-commit Tests..." -ForegroundColor Cyan
Write-Host "================================"

# Run application health tests
Write-Host "Running Application Health Tests..." -ForegroundColor Yellow
$healthResult = & php artisan test tests/Feature/ApplicationHealthTest.php --stop-on-failure
$healthExitCode = $LASTEXITCODE

if ($healthExitCode -ne 0) {
    Write-Host "Application Health Tests failed!" -ForegroundColor Red
    Write-Host "Please fix the failing tests before committing." -ForegroundColor Red
    exit 1
}

# Run inspection calendar tests
Write-Host "Running Inspection Calendar Tests..." -ForegroundColor Yellow
$calendarResult = & php artisan test tests/Feature/Admin/HB837/InspectionCalendarTest.php --stop-on-failure
$calendarExitCode = $LASTEXITCODE

if ($calendarExitCode -ne 0) {
    Write-Host "Inspection Calendar Tests failed!" -ForegroundColor Red
    Write-Host "Please fix the failing tests before committing." -ForegroundColor Red
    exit 1
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
