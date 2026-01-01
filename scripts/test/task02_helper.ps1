#!/usr/bin/env powershell
<#
.SYNOPSIS
Task 02 Testing Helper - PowerShell wrapper for all testing scripts

.DESCRIPTION
Provides easy access to all Task 02 testing scripts with proper error handling

.PARAMETER Action
The testing action to perform:
- status: Quick status check
- pre: Pre-import environment check  
- post: Post-import validation (requires baseline counts)
- mapping: Field mapping analysis
- all: Run all applicable checks

.PARAMETER BaselineTotal
Previous total record count (for post-import validation)

.PARAMETER BaselineTest  
Previous test record count (for post-import validation)

.EXAMPLE
.\task02_helper.ps1 -Action status
.\task02_helper.ps1 -Action pre
.\task02_helper.ps1 -Action post -BaselineTotal 3 -BaselineTest 2
.\task02_helper.ps1 -Action mapping
.\task02_helper.ps1 -Action all
#>

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("status", "pre", "post", "mapping", "all")]
    [string]$Action,
    
    [Parameter(Mandatory=$false)]
    [int]$BaselineTotal = 3,
    
    [Parameter(Mandatory=$false)]
    [int]$BaselineTest = 2
)

$ScriptPath = "scripts\test"
$PhpExe = "php"

function Write-Header {
    param([string]$Title)
    Write-Host ""
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host " $Title" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
}

function Run-Script {
    param([string]$ScriptName, [string[]]$Arguments = @())
    
    $FullPath = "$ScriptPath\$ScriptName"
    
    if (Test-Path $FullPath) {
        Write-Host "Running: $ScriptName" -ForegroundColor Green
        Write-Host "----------------------------------------" -ForegroundColor Gray
        
        try {
            if ($Arguments.Count -gt 0) {
                & $PhpExe $FullPath @Arguments
            } else {
                & $PhpExe $FullPath
            }
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "✅ Completed successfully" -ForegroundColor Green
            } else {
                Write-Host "⚠️  Script completed with warnings (exit code: $LASTEXITCODE)" -ForegroundColor Yellow
            }
        }
        catch {
            Write-Host "❌ Error running script: $($_.Exception.Message)" -ForegroundColor Red
            return $false
        }
    } else {
        Write-Host "❌ Script not found: $FullPath" -ForegroundColor Red
        return $false
    }
    
    Write-Host ""
    return $true
}

# Main execution
Write-Header "TASK 02 TESTING HELPER"

switch ($Action) {
    "status" {
        Write-Host "Performing quick status check..." -ForegroundColor Cyan
        Run-Script "task02_quick_status.php"
    }
    
    "pre" {
        Write-Host "Running pre-import environment check..." -ForegroundColor Cyan
        Run-Script "task02_pre_import_check.php"
    }
    
    "post" {
        Write-Host "Running post-import validation..." -ForegroundColor Cyan
        Write-Host "Baseline counts - Total: $BaselineTotal, Test: $BaselineTest" -ForegroundColor Gray
        Run-Script "task02_post_import_validation.php" @($BaselineTotal, $BaselineTest)
    }
    
    "mapping" {
        Write-Host "Running field mapping analysis..." -ForegroundColor Cyan
        Run-Script "task02_field_mapping_analysis.php"
    }
    
    "all" {
        Write-Host "Running all applicable checks..." -ForegroundColor Cyan
        
        $success = $true
        $success = (Run-Script "task02_quick_status.php") -and $success
        $success = (Run-Script "task02_pre_import_check.php") -and $success
        $success = (Run-Script "task02_field_mapping_analysis.php") -and $success
        
        if ($success) {
            Write-Host "🎉 All checks completed successfully!" -ForegroundColor Green
        } else {
            Write-Host "⚠️  Some checks had issues - review output above" -ForegroundColor Yellow
        }
    }
}

Write-Host "Task 02 testing helper completed." -ForegroundColor Cyan
Write-Host "For post-import validation, use: .\task02_helper.ps1 -Action post -BaselineTotal $BaselineTotal -BaselineTest $BaselineTest" -ForegroundColor Gray
