@echo off
REM Task 02 Testing Helper - Batch file wrapper
REM Usage: task02_test.bat [action]
REM Actions: status, pre, post, mapping, all

setlocal enabledelayedexpansion

set "SCRIPT_PATH=scripts\test"
set "ACTION=%1"

if "%ACTION%"=="" (
    echo Usage: task02_test.bat [action]
    echo Actions:
    echo   status  - Quick status check
    echo   pre     - Pre-import environment check
    echo   post    - Post-import validation ^(requires manual baseline counts^)
    echo   mapping - Field mapping analysis
    echo   all     - Run all applicable checks
    exit /b 1
)

echo ===============================================
echo  TASK 02 TESTING HELPER
echo ===============================================
echo.

if "%ACTION%"=="status" (
    echo Running quick status check...
    php %SCRIPT_PATH%\task02_quick_status.php
) else if "%ACTION%"=="pre" (
    echo Running pre-import environment check...
    php %SCRIPT_PATH%\task02_pre_import_check.php
) else if "%ACTION%"=="post" (
    echo Running post-import validation...
    echo NOTE: Update baseline counts in the script if needed
    php %SCRIPT_PATH%\task02_post_import_validation.php 3 2
) else if "%ACTION%"=="mapping" (
    echo Running field mapping analysis...
    php %SCRIPT_PATH%\task02_field_mapping_analysis.php
) else if "%ACTION%"=="all" (
    echo Running all applicable checks...
    echo.
    echo --- Quick Status ---
    php %SCRIPT_PATH%\task02_quick_status.php
    echo.
    echo --- Pre-Import Check ---
    php %SCRIPT_PATH%\task02_pre_import_check.php
    echo.
    echo --- Field Mapping Analysis ---
    php %SCRIPT_PATH%\task02_field_mapping_analysis.php
) else (
    echo Invalid action: %ACTION%
    echo Valid actions: status, pre, post, mapping, all
    exit /b 1
)

echo.
echo ===============================================
echo Task 02 testing helper completed.
echo ===============================================
