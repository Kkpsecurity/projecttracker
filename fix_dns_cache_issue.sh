#!/bin/bash

echo "=== IMMEDIATE FIX: DNS/Database Cache Error ==="
echo ""

echo "🚨 ISSUE: Cannot resolve 'criustemp.hq.cisadmin.com'"
echo "🔧 SOLUTION: Switch to file-based cache"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "❌ .env file not found!"
    echo "Creating .env from .env.server..."
    cp .env.server .env 2>/dev/null || echo "⚠️  .env.server not found"
fi

echo "1. Updating cache configuration..."
# Update .env to use file cache instead of database
sed -i 's/CACHE_STORE=database/CACHE_STORE=file/g' .env
sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/g' .env

echo "✅ Updated .env:"
echo "   CACHE_STORE=file (was database)"
echo "   QUEUE_CONNECTION=sync (was database)"

echo ""
echo "2. Creating cache directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
chmod -R 755 storage

echo ""
echo "3. Clearing caches safely..."
php artisan config:clear
php artisan route:clear 
php artisan view:clear

echo ""
echo "4. Testing cache clear..."
php artisan cache:clear

echo ""
echo "=== FIX COMPLETE ==="
echo "✅ Cache now uses files instead of database"
echo "✅ Application should work without database DNS resolution"
echo ""
echo "Run this on your server to fix the issue immediately!"
echo ""
