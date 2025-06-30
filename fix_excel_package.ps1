# Laravel Excel Package Upgrade Script for Windows/PowerShell
# Upgrading from maatwebsite/excel ^1.1 to ^3.1
# This will fix the PhpOffice\PhpSpreadsheet error

Write-Host "=== Laravel Excel Package Upgrade Script ===" -ForegroundColor Green
Write-Host "Upgrading from maatwebsite/excel ^1.1 to ^3.1" -ForegroundColor Yellow
Write-Host "This will fix the PhpOffice\PhpSpreadsheet error" -ForegroundColor Yellow
Write-Host ""

# Remove the old vendor directory and lock file to ensure clean install
Write-Host "1. Cleaning up old dependencies..." -ForegroundColor Cyan
if (Test-Path "vendor") { Remove-Item -Recurse -Force "vendor" }
if (Test-Path "composer.lock") { Remove-Item -Force "composer.lock" }

# Update composer dependencies
Write-Host "2. Installing updated dependencies..." -ForegroundColor Cyan
composer install --no-dev --optimize-autoloader

# Clear all Laravel caches
Write-Host "3. Clearing Laravel caches..." -ForegroundColor Cyan
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Publish excel config if needed
Write-Host "4. Publishing Excel configuration..." -ForegroundColor Cyan
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

# Cache config for production
Write-Host "5. Caching configuration..." -ForegroundColor Cyan
php artisan config:cache

Write-Host ""
Write-Host "=== Upgrade Complete ===" -ForegroundColor Green
Write-Host "The maatwebsite/excel package has been upgraded to version 3.1" -ForegroundColor Yellow
Write-Host "This now uses phpoffice/phpspreadsheet instead of the abandoned phpoffice/phpexcel" -ForegroundColor Yellow
Write-Host ""
Write-Host "You can now run: php artisan package:discover --ansi" -ForegroundColor Cyan
Write-Host ""
