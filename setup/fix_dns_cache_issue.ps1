# Laravel DNS/Database Cache Fix Script for Windows PowerShell
# Fixes: SQLSTATE[08006] [7] could not translate host name to address

Write-Host "=== IMMEDIATE FIX: DNS/Database Cache Error ===" -ForegroundColor Green
Write-Host ""

Write-Host "🚨 ISSUE: Cannot resolve 'criustemp.hq.cisadmin.com'" -ForegroundColor Red
Write-Host "🔧 SOLUTION: Switch to file-based cache" -ForegroundColor Yellow
Write-Host ""

# Check if .env exists
if (-not (Test-Path ".env")) {
    Write-Host "❌ .env file not found!" -ForegroundColor Red
    Write-Host "Creating .env from .env.server..." -ForegroundColor Yellow
    if (Test-Path ".env.server") {
        Copy-Item ".env.server" ".env"
        Write-Host "✅ Created .env from .env.server" -ForegroundColor Green
    } else {
        Write-Host "⚠️  .env.server not found" -ForegroundColor Yellow
    }
}

Write-Host "1. Updating cache configuration..." -ForegroundColor Cyan
# Update .env to use file cache instead of database
(Get-Content .env) -replace 'CACHE_STORE=database', 'CACHE_STORE=file' -replace 'QUEUE_CONNECTION=database', 'QUEUE_CONNECTION=sync' | Set-Content .env

Write-Host "✅ Updated .env:" -ForegroundColor Green
Write-Host "   CACHE_STORE=file (was database)" -ForegroundColor White
Write-Host "   QUEUE_CONNECTION=sync (was database)" -ForegroundColor White

Write-Host ""
Write-Host "2. Creating cache directories..." -ForegroundColor Cyan
New-Item -ItemType Directory -Force -Path "storage\framework\cache\data" | Out-Null
New-Item -ItemType Directory -Force -Path "storage\framework\sessions" | Out-Null
New-Item -ItemType Directory -Force -Path "storage\framework\views" | Out-Null
New-Item -ItemType Directory -Force -Path "storage\logs" | Out-Null
Write-Host "✅ Cache directories created" -ForegroundColor Green

Write-Host ""
Write-Host "3. Clearing caches safely..." -ForegroundColor Cyan
php artisan config:clear
php artisan route:clear
php artisan view:clear

Write-Host ""
Write-Host "4. Testing cache clear..." -ForegroundColor Cyan
php artisan cache:clear

Write-Host ""
Write-Host "5. Verifying Laravel works..." -ForegroundColor Cyan
php artisan --version

Write-Host ""
Write-Host "=== FIX COMPLETE ===" -ForegroundColor Green
Write-Host "✅ Cache now uses files instead of database" -ForegroundColor Green
Write-Host "✅ Application should work without database DNS resolution" -ForegroundColor Green
Write-Host ""
Write-Host "Upload this fixed version to your server!" -ForegroundColor Yellow
Write-Host ""
