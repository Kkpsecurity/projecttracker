#!/bin/bash

echo "=== Laravel Excel Package Upgrade Script ==="
echo "Upgrading from maatwebsite/excel ^1.1 to ^3.1"
echo "This will fix the PhpOffice\PhpSpreadsheet error"
echo ""

# Remove the old vendor directory and lock file to ensure clean install
echo "1. Cleaning up old dependencies..."
rm -rf vendor/
rm -f composer.lock

# Update composer dependencies
echo "2. Installing updated dependencies..."
composer install --no-dev --optimize-autoloader

# Clear all Laravel caches
echo "3. Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Publish excel config if needed
echo "4. Publishing Excel configuration..."
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

# Cache config for production
echo "5. Caching configuration..."
php artisan config:cache

echo ""
echo "=== Upgrade Complete ==="
echo "The maatwebsite/excel package has been upgraded to version 3.1"
echo "This now uses phpoffice/phpspreadsheet instead of the abandoned phpoffice/phpexcel"
echo ""
echo "You can now run: php artisan package:discover --ansi"
echo ""
