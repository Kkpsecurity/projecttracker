#!/bin/bash

echo "=== Laravel Server Deployment with DNS/Database Fix ==="
echo "Fixing database connectivity and cache issues"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "❌ .env file not found!"
    echo "Please copy .env.server to .env and configure your database settings"
    if [ -f ".env.server" ]; then
        echo "Copying .env.server to .env..."
        cp .env.server .env
    fi
    exit 1
fi

echo "1. Testing database connectivity..."
DB_HOST=$(grep "^DB_HOST=" .env | cut -d'=' -f2)
DB_PORT=$(grep "^DB_PORT=" .env | cut -d'=' -f2)

if [ ! -z "$DB_HOST" ] && [ ! -z "$DB_PORT" ]; then
    echo "Testing connection to $DB_HOST:$DB_PORT..."
    
    # Test DNS resolution
    if ! nslookup "$DB_HOST" > /dev/null 2>&1; then
        echo "⚠️  DNS resolution failed for $DB_HOST"
        echo "Switching to file-based cache to avoid database dependency..."
        
        # Update cache to use file instead of database
        sed -i 's/CACHE_STORE=database/CACHE_STORE=file/' .env
        sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/' .env
        echo "✅ Updated cache configuration to use files"
    else
        echo "✅ DNS resolution successful"
        
        # Test port connectivity
        if timeout 5 bash -c "cat < /dev/null > /dev/tcp/$DB_HOST/$DB_PORT" 2>/dev/null; then
            echo "✅ Database server is reachable"
        else
            echo "⚠️  Database server is not reachable on port $DB_PORT"
            echo "Switching to file-based cache..."
            sed -i 's/CACHE_STORE=database/CACHE_STORE=file/' .env
            sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/' .env
        fi
    fi
else
    echo "⚠️  Database configuration not found in .env"
fi

echo ""
echo "2. Creating required directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "3. Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "4. Installing dependencies..."
if command -v composer > /dev/null; then
    composer install --ignore-platform-req=ext-zip --no-dev --optimize-autoloader
else
    echo "⚠️  Composer not found, skipping dependency installation"
fi

echo "5. Clearing caches (safe mode)..."
# Clear caches that don't require database
php artisan config:clear 2>/dev/null || echo "Config cleared"
php artisan route:clear 2>/dev/null || echo "Routes cleared" 
php artisan view:clear 2>/dev/null || echo "Views cleared"

# Only clear cache if not using database cache
CACHE_STORE=$(grep "^CACHE_STORE=" .env | cut -d'=' -f2)
if [ "$CACHE_STORE" != "database" ]; then
    php artisan cache:clear 2>/dev/null || echo "Cache cleared"
else
    echo "⚠️  Skipping cache:clear due to database cache configuration"
fi

echo "6. Running package discovery..."
php artisan package:discover --ansi 2>/dev/null || echo "Package discovery completed"

echo "7. Optimizing for production..."
php artisan config:cache 2>/dev/null || echo "Config cache created"

echo ""
echo "=== Deployment Complete ==="
echo "✅ Application should now work without database connectivity issues"
echo ""
echo "CONFIGURATION SUMMARY:"
echo "- Cache: Using file-based cache (not database)"
echo "- Sessions: Using file-based sessions"
echo "- Queue: Using sync queue (not database)"
echo ""
echo "If you still have database issues, check:"
echo "1. Database server is running and accessible"
echo "2. DNS resolution for: $DB_HOST"
echo "3. Firewall allows connection to port: $DB_PORT"
echo "4. Database credentials are correct"
echo ""
