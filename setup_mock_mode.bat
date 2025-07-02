@echo off
echo DataTables Mock Mode Setup
echo ==========================
echo.
echo Choose an option:
echo 1. Enable Mock Mode (for testing without database)
echo 2. Disable Mock Mode (use real database)
echo 3. Test current setup
echo.
set /p choice="Enter your choice (1-3): "

if "%choice%"=="1" goto :enable_mock
if "%choice%"=="2" goto :disable_mock
if "%choice%"=="3" goto :test_setup
goto :invalid

:enable_mock
echo Enabling mock mode...
powershell -Command "(Get-Content routes\admin.php) -replace '// use App\\Http\\Controllers\\Admin\\MockPlotsController;', 'use App\\Http\\Controllers\\Admin\\MockPlotsController;' | Set-Content routes\admin.php"
powershell -Command "(Get-Content routes\admin.php) -replace 'Route::get\(''/datatable'', \[PlotsController::class, ''datatable''\]\)', 'Route::get(''/datatable'', [MockPlotsController::class, ''datatable''])' | Set-Content routes\admin.php"
echo ✓ Mock mode enabled. DataTables will use test data.
goto :end

:disable_mock
echo Disabling mock mode...
powershell -Command "(Get-Content routes\admin.php) -replace 'use App\\Http\\Controllers\\Admin\\MockPlotsController;', '// use App\\Http\\Controllers\\Admin\\MockPlotsController;' | Set-Content routes\admin.php"
powershell -Command "(Get-Content routes\admin.php) -replace 'Route::get\(''/datatable'', \[MockPlotsController::class, ''datatable''\]\)', 'Route::get(''/datatable'', [PlotsController::class, ''datatable''])' | Set-Content routes\admin.php"
echo ✓ Mock mode disabled. DataTables will use real database.
goto :end

:test_setup
echo Testing current setup...
php test_datatables_debug.php
goto :end

:invalid
echo Invalid choice. Please run the script again.
goto :end

:end
echo.
pause
