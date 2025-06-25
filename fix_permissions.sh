#!/bin/bash

# Script to fix Laravel storage permissions
# Run this on the Linux server where the web server is running

echo "=== Laravel Storage Permissions Fix ==="

# Navigate to the project directory
cd /var/www/projecttracker

echo "Current directory: $(pwd)"

# Check current permissions
echo "Current storage permissions:"
ls -la storage/

# Fix storage permissions
echo "Fixing storage directory permissions..."
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/

# Also fix bootstrap/cache if it exists
if [ -d "bootstrap/cache" ]; then
    echo "Fixing bootstrap/cache permissions..."
    sudo chown -R www-data:www-data bootstrap/cache/
    sudo chmod -R 775 bootstrap/cache/
fi

# Check the results
echo "New storage permissions:"
ls -la storage/

echo "=== Permissions fix completed ==="
echo "You should now be able to access the Laravel application without storage permission errors."
