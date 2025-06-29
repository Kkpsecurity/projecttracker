# 🚀 Fresh Laravel + AdminLTE Setup Script
# Project: ProjectTracker Fresh Installation
# Date: June 27, 2025

Write-Host "🚀 Starting Fresh Laravel + AdminLTE Setup..." -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan

# Check if running from correct directory
$currentLocation = Get-Location
Write-Host "📍 Current location: $currentLocation" -ForegroundColor Yellow

# Navigate to Laragon www directory
Write-Host "📁 Navigating to c:\laragon\www..." -ForegroundColor Yellow
Set-Location "c:\laragon\www"

# Check if projecttracker_fresh already exists
if (Test-Path "projecttracker_fresh") {
    Write-Host "⚠️  Directory 'projecttracker_fresh' already exists!" -ForegroundColor Red
    $response = Read-Host "Do you want to remove it and start fresh? (y/N)"
    if ($response -eq "y" -or $response -eq "Y") {
        Write-Host "🗑️  Removing existing projecttracker_fresh..." -ForegroundColor Yellow
        Remove-Item "projecttracker_fresh" -Recurse -Force
    } else {
        Write-Host "❌ Setup cancelled." -ForegroundColor Red
        exit
    }
}

Write-Host "📦 Creating fresh Laravel project..." -ForegroundColor Green
composer create-project laravel/laravel projecttracker_fresh

if (Test-Path "projecttracker_fresh") {
    Write-Host "✅ Laravel project created successfully!" -ForegroundColor Green
} else {
    Write-Host "❌ Failed to create Laravel project!" -ForegroundColor Red
    exit
}

# Navigate to new project
Write-Host "📁 Navigating to projecttracker_fresh..." -ForegroundColor Yellow
Set-Location "projecttracker_fresh"

Write-Host "🔑 Generating application key..." -ForegroundColor Green
php artisan key:generate

Write-Host "📦 Installing core dependencies..." -ForegroundColor Green

# Install Laravel UI for authentication
Write-Host "  📦 Installing Laravel UI..." -ForegroundColor Cyan
composer require laravel/ui

# Install AdminLTE
Write-Host "  📦 Installing AdminLTE..." -ForegroundColor Cyan
composer require jeroennoten/laravel-adminlte

# Install other packages from original project
Write-Host "  📦 Installing additional packages..." -ForegroundColor Cyan
composer require barryvdh/laravel-dompdf
composer require laracasts/flash
composer require maatwebsite/excel

Write-Host "🔧 Setting up authentication and AdminLTE..." -ForegroundColor Green

# Generate authentication scaffolding
Write-Host "  🔐 Generating authentication scaffolding..." -ForegroundColor Cyan
php artisan ui bootstrap --auth

# Install AdminLTE with full setup
Write-Host "  🎨 Installing AdminLTE with enhanced setup..." -ForegroundColor Cyan
php artisan adminlte:install --type=enhanced --with=auth_views

# Publish AdminLTE configuration
Write-Host "  📋 Publishing AdminLTE configuration..." -ForegroundColor Cyan
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\AdminLteServiceProvider"

Write-Host "⚙️  Configuring environment..." -ForegroundColor Green

# Create .env configuration
$envContent = @"
APP_NAME=ProjectTracker
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://projecttracker_fresh.test

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=criustemp.hq.cisadmin.com
DB_PORT=5432
DB_DATABASE=projecttracker_fresh
DB_USERNAME=projecttracker
DB_PASSWORD=">po/xDG3~.07a?Xd"

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=480
SESSION_DOMAIN=.projecttracker_fresh.test

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
"@

# Backup original .env and create new one
if (Test-Path ".env") {
    Copy-Item ".env" ".env.backup"
}

$envContent | Out-File -FilePath ".env" -Encoding UTF8

# Generate new app key
Write-Host "🔑 Generating new application key..." -ForegroundColor Green
php artisan key:generate

Write-Host "🧹 Clearing caches..." -ForegroundColor Green
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

Write-Host "📊 Testing database connection..." -ForegroundColor Green
Write-Host "⚠️  Note: Make sure 'projecttracker_fresh' database exists in PostgreSQL!" -ForegroundColor Yellow

$dbTest = php artisan migrate:status 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Database connection successful!" -ForegroundColor Green

    Write-Host "🏗️  Running migrations..." -ForegroundColor Green
    php artisan migrate

    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Migrations completed successfully!" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Migrations had issues, but continuing..." -ForegroundColor Yellow
    }
} else {
    Write-Host "⚠️  Database connection issues detected:" -ForegroundColor Yellow
    Write-Host $dbTest -ForegroundColor Red
    Write-Host "📋 Please create 'projecttracker_fresh' database manually and re-run migrations." -ForegroundColor Yellow
}

Write-Host "👤 Creating test admin user..." -ForegroundColor Green
$tinkerScript = @"
`$user = new App\Models\User();
`$user->name = 'Admin';
`$user->email = 'admin@test.com';
`$user->password = bcrypt('password123');
`$user->save();
echo 'Admin user created successfully!';
exit;
"@

$tinkerScript | php artisan tinker

Write-Host "🎉 Setup Complete!" -ForegroundColor Green
Write-Host "===================" -ForegroundColor Cyan

Write-Host "📋 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Start the development server:" -ForegroundColor White
Write-Host "   php artisan serve --host=127.0.0.1 --port=8000" -ForegroundColor Cyan

Write-Host "2. Test these URLs:" -ForegroundColor White
Write-Host "   http://127.0.0.1:8000 - Laravel welcome" -ForegroundColor Cyan
Write-Host "   http://127.0.0.1:8000/login - AdminLTE login" -ForegroundColor Cyan
Write-Host "   http://127.0.0.1:8000/home - Dashboard" -ForegroundColor Cyan

Write-Host "3. Login credentials:" -ForegroundColor White
Write-Host "   Email: admin@test.com" -ForegroundColor Cyan
Write-Host "   Password: password123" -ForegroundColor Cyan

Write-Host "⚠️  Important: Test login thoroughly for CSRF issues!" -ForegroundColor Yellow

Write-Host "🎯 Success Criteria:" -ForegroundColor Green
Write-Host "   ✅ No 419 CSRF errors on login" -ForegroundColor White
Write-Host "   ✅ AdminLTE interface loads" -ForegroundColor White
Write-Host "   ✅ Dashboard accessible after login" -ForegroundColor White

Write-Host "📁 Project location: $(Get-Location)" -ForegroundColor Yellow
Write-Host "🚀 Ready for migration phase!" -ForegroundColor Green
