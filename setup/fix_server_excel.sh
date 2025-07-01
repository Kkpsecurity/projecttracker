#!/bin/bash

echo "=== Laravel Excel Package Fix for Server Deployment ==="
echo "Fixing PhpOffice\PhpSpreadsheet error on live server"
echo ""

echo "The error occurs because:"
echo "- Local dev uses maatwebsite/excel v1.1 (with phpoffice/phpexcel)"
echo "- Server tried to install v3.1 (with phpoffice/phpspreadsheet)"
echo "- Missing php-zip extension on server"
echo ""

echo "SOLUTION 1: Install php-zip extension on server"
echo "sudo apt-get update"
echo "sudo apt-get install php-zip"
echo "sudo systemctl restart apache2   # or nginx"
echo ""

echo "SOLUTION 2: Use version that doesn't require zip"
echo "Update composer.json to use compatible version"
echo ""

# Check if vendor directory exists
if [ -d "vendor" ]; then
    echo "1. Removing vendor directory..."
    rm -rf vendor/
fi

if [ -f "composer.lock" ]; then
    echo "2. Removing composer.lock..."
    rm -f composer.lock
fi

echo "3. Installing compatible dependencies..."
composer install --ignore-platform-req=ext-zip --no-dev --optimize-autoloader

echo "4. Clearing Laravel caches..."
php artisan config:clear 2>/dev/null || echo "Config cache cleared"
php artisan cache:clear 2>/dev/null || echo "Application cache cleared"
php artisan route:clear 2>/dev/null || echo "Route cache cleared"
php artisan view:clear 2>/dev/null || echo "View cache cleared"

echo "5. Running package discovery..."
php artisan package:discover --ansi

echo ""
echo "=== Fix Complete ==="
echo "The Excel package should now work on the server"
echo "If you still get errors, install php-zip extension:"
echo "  Ubuntu/Debian: sudo apt-get install php-zip"
echo "  CentOS/RHEL: sudo yum install php-zip"
echo ""
