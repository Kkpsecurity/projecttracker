# PowerShell Test Runner for Laravel Application
# Usage: .\run-tests.ps1 [quick|coverage|parallel|stop|verbose|help]

param(
    [string]$Type = "full",
    [switch]$Quick,
    [switch]$Coverage,
    [switch]$Parallel,
    [switch]$Stop,
    [switch]$Verbose,
    [switch]$Help
)

# Color functions
function Write-ColorOutput {
    param(
        [string]$Message,
        [string]$ForegroundColor = "White"
    )
    Write-Host $Message -ForegroundColor $ForegroundColor
}

function Write-Header {
    param([string]$Title)
    Write-Host ""
    Write-ColorOutput "================================================" "Blue"
    Write-ColorOutput "🧪 $Title" "Blue"
    Write-ColorOutput "================================================" "Blue"
    Write-Host ""
}

function Test-Result {
    param(
        [string]$TestName,
        [int]$ExitCode
    )
    if ($ExitCode -eq 0) {
        Write-ColorOutput "✅ $TestName passed!" "Green"
        return $true
    } else {
        Write-ColorOutput "❌ $TestName failed!" "Red"
        return $false
    }
}

# Show help
if ($Help) {
    Write-Host "Usage: .\run-tests.ps1 [options]"
    Write-Host ""
    Write-Host "Options:"
    Write-Host "  -Quick     - Run only critical tests"
    Write-Host "  -Coverage  - Generate code coverage report"
    Write-Host "  -Parallel  - Run tests in parallel"
    Write-Host "  -Stop      - Stop on first failure"
    Write-Host "  -Verbose   - Verbose output"
    Write-Host "  -Help      - Show this help"
    exit 0
}

# Check if we're in a Laravel project
if (-not (Test-Path "artisan")) {
    Write-ColorOutput "❌ Error: Not in a Laravel project directory" "Red"
    exit 1
}

Write-Header "LARAVEL APPLICATION TEST SUITE"

# Build test command options
$TestOptions = @()
if ($Stop) { $TestOptions += "--stop-on-failure" }
if ($Verbose) { $TestOptions += "--verbose" }
if ($Parallel) { $TestOptions += "--parallel" }
if ($Coverage) {
    $TestOptions += "--coverage-html=storage/app/coverage"
    $TestOptions += "--coverage-clover=storage/app/coverage.xml"
}

# Initialize counters
$TotalTests = 0
$PassedTests = 0
$FailedTests = 0

Write-Header "PRE-TEST ENVIRONMENT CHECKS"

Write-ColorOutput "🔍 Checking PHP version..." "Cyan"
php --version | Select-Object -First 1

Write-ColorOutput "🔍 Checking Laravel version..." "Cyan"
php artisan --version

Write-ColorOutput "🔍 Clearing application caches..." "Cyan"
php artisan config:clear *>$null
php artisan route:clear *>$null
php artisan view:clear *>$null
Write-ColorOutput "✅ Cache clearing passed!" "Green"

Write-ColorOutput "🔍 Testing database connection..." "Cyan"
$dbResult = php artisan migrate:status *>$null
if ($LASTEXITCODE -eq 0) {
    Write-ColorOutput "✅ Database connection passed!" "Green"
} else {
    Write-ColorOutput "❌ Database connection failed!" "Red"
}

Write-ColorOutput "🔍 Checking critical routes..." "Cyan"
$routeResult1 = php artisan route:list --name=admin.dashboard *>$null
$routeResult2 = php artisan route:list --name=inspection-calendar *>$null
if ($LASTEXITCODE -eq 0) {
    Write-ColorOutput "✅ Route availability passed!" "Green"
} else {
    Write-ColorOutput "❌ Route availability failed!" "Red"
}

if ($Quick) {
    Write-Header "QUICK TEST SUITE (CRITICAL TESTS ONLY)"

    Write-ColorOutput "🧪 Running Application Health Tests..." "Yellow"
    $cmd = "php artisan test tests/Feature/ApplicationHealthTest.php $($TestOptions -join ' ')"
    Invoke-Expression $cmd
    if (Test-Result "Application Health Tests" $LASTEXITCODE) {
        $PassedTests++
    } else {
        $FailedTests++
    }
    $TotalTests++

    Write-ColorOutput "📅 Running Inspection Calendar Tests..." "Yellow"
    $cmd = "php artisan test tests/Feature/Admin/HB837/InspectionCalendarTest.php $($TestOptions -join ' ')"
    Invoke-Expression $cmd
    if (Test-Result "Inspection Calendar Tests" $LASTEXITCODE) {
        $PassedTests++
    } else {
        $FailedTests++
    }
    $TotalTests++

} else {
    Write-Header "FULL TEST SUITE"

    Write-ColorOutput "🧪 Running Application Health Tests..." "Yellow"
    $cmd = "php artisan test tests/Feature/ApplicationHealthTest.php $($TestOptions -join ' ')"
    Invoke-Expression $cmd
    if (Test-Result "Application Health Tests" $LASTEXITCODE) {
        $PassedTests++
    } else {
        $FailedTests++
    }
    $TotalTests++

    Write-ColorOutput "📅 Running Inspection Calendar Tests..." "Yellow"
    $cmd = "php artisan test tests/Feature/Admin/HB837/InspectionCalendarTest.php $($TestOptions -join ' ')"
    Invoke-Expression $cmd
    if (Test-Result "Inspection Calendar Tests" $LASTEXITCODE) {
        $PassedTests++
    } else {
        $FailedTests++
    }
    $TotalTests++

    Write-ColorOutput "🏗️ Running HB837 Controller Tests..." "Yellow"
    if (Test-Path "tests/Feature/HB837ControllerTest.php") {
        $cmd = "php artisan test tests/Feature/HB837ControllerTest.php $($TestOptions -join ' ')"
        Invoke-Expression $cmd
        if (Test-Result "HB837 Controller Tests" $LASTEXITCODE) {
            $PassedTests++
        } else {
            $FailedTests++
        }
        $TotalTests++
    } else {
        Write-ColorOutput "⚠️ HB837 Controller Tests not found, skipping..." "Yellow"
    }

    Write-ColorOutput "📊 Running Import/Export Tests..." "Yellow"
    if (Test-Path "tests/Feature/HB837ImportExportTest.php") {
        $cmd = "php artisan test tests/Feature/HB837ImportExportTest.php $($TestOptions -join ' ')"
        Invoke-Expression $cmd
        if (Test-Result "Import/Export Tests" $LASTEXITCODE) {
            $PassedTests++
        } else {
            $FailedTests++
        }
        $TotalTests++
    } else {
        Write-ColorOutput "⚠️ Import/Export Tests not found, skipping..." "Yellow"
    }

    Write-ColorOutput "🔧 Running Unit Tests..." "Yellow"
    if ((Test-Path "tests/Unit") -and (Get-ChildItem "tests/Unit" -Recurse -File).Count -gt 0) {
        $cmd = "php artisan test tests/Unit $($TestOptions -join ' ')"
        Invoke-Expression $cmd
        if (Test-Result "Unit Tests" $LASTEXITCODE) {
            $PassedTests++
        } else {
            $FailedTests++
        }
        $TotalTests++
    } else {
        Write-ColorOutput "⚠️ No Unit Tests found, skipping..." "Yellow"
    }

    Write-ColorOutput "🌟 Running Feature Tests..." "Yellow"
    if (Test-Path "tests/Feature/ExampleTest.php") {
        $cmd = "php artisan test tests/Feature/ExampleTest.php $($TestOptions -join ' ')"
        Invoke-Expression $cmd
        if (Test-Result "Example Feature Tests" $LASTEXITCODE) {
            $PassedTests++
        } else {
            $FailedTests++
        }
        $TotalTests++
    } else {
        Write-ColorOutput "⚠️ Example Feature Tests not found, skipping..." "Yellow"
    }
}

Write-Header "POST-TEST ENVIRONMENT CHECKS"

Write-ColorOutput "🔍 Checking application state after tests..." "Cyan"
php artisan route:list --name=admin.dashboard *>$null
if ($LASTEXITCODE -eq 0) {
    Write-ColorOutput "✅ Routes still accessible!" "Green"
} else {
    Write-ColorOutput "❌ Routes accessibility failed!" "Red"
}

Write-ColorOutput "🔍 Testing database integrity..." "Cyan"
php artisan migrate:status *>$null
if ($LASTEXITCODE -eq 0) {
    Write-ColorOutput "✅ Database integrity passed!" "Green"
} else {
    Write-ColorOutput "❌ Database integrity failed!" "Red"
}

Write-ColorOutput "📊 Collecting application statistics..." "Cyan"
try {
    $ProjectCount = php artisan tinker --execute="echo \App\Models\HB837::count();" 2>$null | Select-Object -Last 1
    $UserCount = php artisan tinker --execute="echo \App\Models\User::count();" 2>$null | Select-Object -Last 1
    $ScheduledCount = php artisan tinker --execute="echo \App\Models\HB837::whereNotNull('scheduled_date_of_inspection')->count();" 2>$null | Select-Object -Last 1

    if ($ProjectCount -and $UserCount -and $ScheduledCount) {
        Write-ColorOutput "📈 Application Stats: $UserCount users, $ProjectCount projects, $ScheduledCount scheduled inspections" "Green"
    }
} catch {
    Write-ColorOutput "⚠️ Could not collect application statistics" "Yellow"
}

Write-Header "TEST RESULTS SUMMARY"

if ($FailedTests -eq 0) {
    Write-ColorOutput "🎉 ALL TESTS PASSED! ($PassedTests/$TotalTests)" "Green"
    Write-ColorOutput "✨ Application is ready for deployment!" "Green"
    $ExitCode = 0
} else {
    Write-ColorOutput "❌ SOME TESTS FAILED! ($PassedTests/$TotalTests passed, $FailedTests failed)" "Red"
    Write-ColorOutput "🔧 Please fix the failing tests before proceeding." "Yellow"
    $ExitCode = 1
}

if ($Coverage) {
    Write-ColorOutput "📊 Code coverage report generated in storage/app/coverage/" "Cyan"
}

Write-ColorOutput "⏱️ Test suite completed at $(Get-Date)" "Blue"

# Additional tips
Write-Header "HELPFUL COMMANDS"
Write-ColorOutput "💡 Run specific test: php artisan test tests/Feature/[TestName].php" "Cyan"
Write-ColorOutput "💡 Run with coverage: php artisan test --coverage" "Cyan"
Write-ColorOutput "💡 Run in parallel: php artisan test --parallel" "Cyan"
Write-ColorOutput "💡 Quick test: .\run-tests.ps1 -Quick" "Cyan"

exit $ExitCode
