# Complete Server Deployment Script for PowerShell
# Finishing deployment for projecttracker.hb837training.com

Write-Host "=== COMPLETE SERVER DEPLOYMENT SCRIPT ===" -ForegroundColor Green
Write-Host "Finishing deployment for projecttracker.hb837training.com" -ForegroundColor Yellow
Write-Host ""

# Change to the project directory (adjust path as needed for your local environment)
$projectPath = "c:\laragon\www\projecttracker_fresh"

if (Test-Path $projectPath) {
    Set-Location $projectPath
    Write-Host "✅ Changed to project directory: $projectPath" -ForegroundColor Green
} else {
    Write-Host "❌ Could not find project directory: $projectPath" -ForegroundColor Red
    Write-Host "Please update the `$projectPath variable in this script" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# Step 1: Run the PHP deployment script
Write-Host "1. Running complete deployment PHP script..." -ForegroundColor Cyan
php complete_server_deployment.php

# Check if the PHP script succeeded
if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY! 🎉" -ForegroundColor Green
    Write-Host ""
    Write-Host "Your application is now ready for server deployment!" -ForegroundColor Green
    Write-Host ""
    Write-Host "For server deployment (via SSH):" -ForegroundColor Cyan
    Write-Host "1. Upload this project to your server" -ForegroundColor White
    Write-Host "2. Run: bash complete_deployment.sh" -ForegroundColor White
    Write-Host "3. Or run: php complete_server_deployment.php" -ForegroundColor White
    Write-Host ""
    Write-Host "Local testing:" -ForegroundColor Cyan
    Write-Host "1. Visit: http://localhost/projecttracker_fresh/public" -ForegroundColor White
    Write-Host "2. Test login with: richievc@gmail.com / Secure`$101" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "❌ Deployment script encountered errors" -ForegroundColor Red
    Write-Host ""
    Write-Host "Manual troubleshooting steps:" -ForegroundColor Yellow
    Write-Host "1. Check database connection:" -ForegroundColor White
    Write-Host "   php artisan migrate:status" -ForegroundColor Gray
    Write-Host ""
    Write-Host "2. Add role column manually if needed:" -ForegroundColor White
    Write-Host "   php artisan tinker" -ForegroundColor Gray
    Write-Host "   >>> Illuminate\Support\Facades\DB::statement(`"ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'editor'`");" -ForegroundColor Gray
    Write-Host "   >>> exit" -ForegroundColor Gray
    Write-Host ""
    Write-Host "3. Run user seeder:" -ForegroundColor White
    Write-Host "   php artisan db:seed --class=UserSeeder --force" -ForegroundColor Gray
    Write-Host ""
    Write-Host "4. Clear caches:" -ForegroundColor White
    Write-Host "   php artisan config:clear && php artisan cache:clear" -ForegroundColor Gray
    Write-Host ""
}
