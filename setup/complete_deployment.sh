#!/bin/bash

echo "=== COMPLETE SERVER DEPLOYMENT SCRIPT ==="
echo "Finishing deployment for projecttracker.hb837training.com"
echo ""

# Change to the project directory (adjust path as needed)
cd /home/forge/projecttracker.hb837training.com || {
    echo "❌ Could not change to project directory"
    echo "Please update the path in this script to match your deployment directory"
    exit 1
}

echo "✅ Changed to project directory: $(pwd)"
echo ""

# Step 1: Run the PHP deployment script
echo "1. Running complete deployment PHP script..."
php complete_server_deployment.php

# Check if the PHP script succeeded
if [ $? -eq 0 ]; then
    echo ""
    echo "🎉 DEPLOYMENT COMPLETED SUCCESSFULLY! 🎉"
    echo ""
    echo "Your application is now live at: https://projecttracker.hb837training.com"
    echo ""
    echo "Next steps:"
    echo "1. Visit the website to verify it loads"
    echo "2. Test login with: richievc@gmail.com / Secure\$101"
    echo "3. Verify all admin features work"
    echo "4. Test the HB837 system functionality"
    echo ""
else
    echo ""
    echo "❌ Deployment script encountered errors"
    echo ""
    echo "Manual troubleshooting steps:"
    echo "1. Check database connection:"
    echo "   php artisan migrate:status"
    echo ""
    echo "2. Add role column manually if needed:"
    echo "   php artisan tinker"
    echo "   >>> Illuminate\Support\Facades\DB::statement(\"ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'editor'\");"
    echo "   >>> exit"
    echo ""
    echo "3. Run user seeder:"
    echo "   php artisan db:seed --class=UserSeeder --force"
    echo ""
    echo "4. Clear caches:"
    echo "   php artisan config:clear && php artisan cache:clear"
    echo ""
fi
