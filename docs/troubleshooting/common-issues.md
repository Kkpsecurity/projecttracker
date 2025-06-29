# Common Issues & Troubleshooting

**Project Tracker** troubleshooting guide for common issues and their solutions.

## 🚨 Authentication & Access Issues

### Issue: Cannot Access Admin Panel
**Symptoms**: Redirected to login, login fails, or access denied

#### Solution 1: Check User Account
```bash
# Verify user exists and has admin privileges
php artisan tinker
> User::where('email', 'your-email@domain.com')->first()
> $user = User::find(1)
> $user->is_admin = true
> $user->is_active = true
> $user->save()
```

#### Solution 2: Reset Admin Password
```bash
# Reset password for admin user
php artisan tinker
> $user = User::where('email', 'admin@example.com')->first()
> $user->password = Hash::make('newpassword')
> $user->save()
```

#### Solution 3: Create New Admin User
```bash
# Create new admin user via seeder
php artisan db:seed --class=UsersTableSeeder

# Or create manually
php artisan tinker
> User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'is_admin' => true,
    'is_active' => true,
    'email_verified_at' => now()
])
```

### Issue: CSRF Token Mismatch
**Symptoms**: 419 errors on form submissions

#### Solution:
```bash
# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan session:clear

# Check .env session configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false  # Set to true only if using HTTPS
```

## 💾 Database Issues

### Issue: Database Connection Failed
**Symptoms**: Database connection errors, migration failures

#### Solution 1: Check Database Configuration
```bash
# Verify .env database settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projecttracker
DB_USERNAME=root
DB_PASSWORD=

# Test connection manually
mysql -h 127.0.0.1 -u root -p projecttracker
```

#### Solution 2: Database Service Issues
```bash
# Windows (Laragon)
# Open Laragon -> Start All
# Check MySQL service in Task Manager

# Linux
sudo systemctl status mysql
sudo systemctl restart mysql

# macOS
brew services restart mysql
```

#### Solution 3: Recreate Database
```bash
# Drop and recreate database
mysql -u root -p -e "DROP DATABASE IF EXISTS projecttracker"
mysql -u root -p -e "CREATE DATABASE projecttracker"

# Re-run migrations
php artisan migrate:fresh --seed
```

### Issue: Migration Errors
**Symptoms**: Migration fails, table already exists errors

#### Solution:
```bash
# Check migration status
php artisan migrate:status

# Rollback and re-run migrations
php artisan migrate:rollback
php artisan migrate

# Fresh migration (WARNING: This will delete all data)
php artisan migrate:fresh --seed
```

### Issue: Foreign Key Constraint Errors
**Symptoms**: Cannot delete records, foreign key violations

#### Solution:
```bash
# Temporarily disable foreign key checks
php artisan tinker
> DB::statement('SET FOREIGN_KEY_CHECKS=0')
> // Perform your operation
> DB::statement('SET FOREIGN_KEY_CHECKS=1')
```

## 🎨 AdminLTE & UI Issues

### Issue: AdminLTE Assets Not Loading (404 Errors)
**Symptoms**: Missing CSS files, 404 errors for vendor assets like `icheck-bootstrap.min.css`

#### Solution 1: Check Application URL Configuration
```bash
# The issue occurs when accessing the app from a different URL than configured
# For example: accessing projecttracker_fresh.test but .env has projecttracker.test

# Check current .env configuration
grep APP_URL .env

# Update APP_URL to match your access URL
# Edit .env file:
APP_URL=http://projecttracker_fresh.test  # Match your actual URL

# Clear configuration cache
php artisan config:clear
php artisan cache:clear
```

#### Solution 2: Republish AdminLTE Assets
```bash
# Republish all AdminLTE vendor assets
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\AdminLteServiceProvider" --tag=assets --force

# Verify assets exist
ls -la public/vendor/icheck-bootstrap/
# Should show: icheck-bootstrap.min.css

# Test direct access to asset
curl -I http://your-domain.test/vendor/icheck-bootstrap/icheck-bootstrap.min.css
```

#### Solution 3: Laragon Virtual Host Configuration
```bash
# For Laragon users, ensure virtual host matches .env
# Check Laragon -> Apache -> sites-enabled -> auto.projecttracker.conf
# Or use Laragon's auto virtual hosts feature

# Restart Laragon services after changes
# Laragon -> Stop All -> Start All
```

### Issue: AdminLTE Styles Not Loading
**Symptoms**: Broken layout, missing styles, default HTML appearance

#### Solution 1: Asset Compilation
```bash
# Recompile assets
npm run dev

# For production
npm run production

# Clear browser cache
# Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
```

#### Solution 2: Check Asset Files
```bash
# Verify AdminLTE files exist
ls -la public/vendor/adminlte/
ls -la public/css/
ls -la public/js/

# Republish AdminLTE assets if missing
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\AdminLteServiceProvider" --tag=assets --force
```

#### Solution 3: CDN Issues
```html
<!-- Check if CDN resources are accessible -->
<!-- In browser developer tools, verify these load: -->
https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css
https://code.jquery.com/jquery-3.6.0.min.js
```

### Issue: DataTables Not Working
**Symptoms**: Tables not sortable, search not working, pagination missing

#### Solution 1: JavaScript Errors
```javascript
// Open browser developer console (F12)
// Check for JavaScript errors
// Common issues:
// - jQuery not loaded
// - DataTables scripts missing
// - CSRF token errors
```

#### Solution 2: AJAX Endpoint Issues
```bash
# Verify DataTables routes exist
php artisan route:list | grep datatable

# Test AJAX endpoint manually
curl -X POST http://projecttracker.test/admin/users/datatable \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token-here"
```

#### Solution 3: Database Query Issues
```php
// Check controller datatable method
// Common issues:
// - Missing columns in select
// - Invalid relationships
// - Incorrect search fields
```

## 📁 File Upload & Storage Issues

### Issue: File Upload Fails
**Symptoms**: File upload errors, 500 errors on file operations

#### Solution 1: Storage Permissions
```bash
# Linux/macOS
chmod -R 775 storage/app/public
chown -R www-data:www-data storage/app/public

# Windows (usually not needed, but check if folder exists)
mkdir storage\app\public
```

#### Solution 2: Storage Link
```bash
# Create symbolic link
php artisan storage:link

# Verify link exists
ls -la public/storage  # Should point to ../storage/app/public
```

#### Solution 3: File Size Limits
```php
// Check PHP configuration
php -i | grep upload_max_filesize
php -i | grep post_max_size
php -i | grep max_execution_time

// Increase limits in php.ini if needed:
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

### Issue: Images Not Displaying
**Symptoms**: Broken image links, 404 errors for uploaded files

#### Solution:
```bash
# Verify storage link
php artisan storage:link

# Check file permissions
ls -la storage/app/public/

# Verify .htaccess in public folder allows access
# Ensure web server has read access to public/storage
```

## ⚡ Performance Issues

### Issue: Slow Page Load Times
**Symptoms**: Pages take long to load, database queries are slow

#### Solution 1: Enable Query Optimization
```php
// Add to .env for debugging
DB_LOG_QUERIES=true
LOG_LEVEL=debug

// Check logs/laravel.log for slow queries
// Optimize queries with proper indexes
```

#### Solution 2: Enable Caching
```bash
# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Enable database query caching
# Add to .env:
CACHE_DRIVER=file  # or redis for better performance
```

#### Solution 3: Optimize Assets
```bash
# Minify assets for production
npm run production

# Enable gzip compression in web server
# Apache: mod_deflate
# Nginx: gzip on;
```

### Issue: High Memory Usage
**Symptoms**: Out of memory errors, slow performance

#### Solution:
```php
// Increase PHP memory limit
ini_set('memory_limit', '256M');

// Or in php.ini:
memory_limit = 256M

// Optimize large dataset queries
// Use chunking for large operations:
Client::chunk(100, function ($clients) {
    foreach ($clients as $client) {
        // Process client
    }
});
```

## 🔧 Development Environment Issues

### Issue: Composer Install Fails
**Symptoms**: Composer errors, dependency conflicts

#### Solution 1: Clear Composer Cache
```bash
# Clear composer cache
composer clear-cache

# Update composer itself
composer self-update

# Install with verbose output
composer install --verbose
```

#### Solution 2: PHP Version Issues
```bash
# Check PHP version
php -v

# Ensure PHP 8.3+ is being used
# Update composer.json if needed:
"require": {
    "php": "^8.3"
}
```

#### Solution 3: Memory Issues
```bash
# Increase composer memory limit
COMPOSER_MEMORY_LIMIT=-1 composer install

# Or permanently in php.ini:
memory_limit = -1
```

### Issue: NPM Install Fails
**Symptoms**: Node package installation errors

#### Solution:
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and package-lock.json
rm -rf node_modules package-lock.json

# Reinstall packages
npm install

# Use specific Node version if needed
nvm use 18
npm install
```

## 🌐 Web Server Issues

### Issue: 404 Errors for Routes
**Symptoms**: Laravel routes return 404, only homepage works

#### Solution 1: URL Rewriting (Apache)
```apache
# Ensure .htaccess exists in public folder
# Content should include:
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

#### Solution 2: Virtual Host Configuration
```apache
# Ensure DocumentRoot points to public folder
DocumentRoot "/path/to/projecttracker/public"

# Not the application root:
# DocumentRoot "/path/to/projecttracker"  # WRONG
```

#### Solution 3: Nginx Configuration
```nginx
# Ensure try_files directive is correct
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Issue: 500 Internal Server Error
**Symptoms**: Generic 500 errors, white screen

#### Solution:
```bash
# Enable debug mode temporarily
# In .env:
APP_DEBUG=true
APP_ENV=local

# Check error logs
tail -f storage/logs/laravel.log
tail -f /var/log/apache2/error.log  # Linux
tail -f /var/log/nginx/error.log    # Nginx

# Common causes:
# - Missing .htaccess
# - File permission issues
# - PHP errors
# - Missing PHP extensions
```

## 📧 Email Configuration Issues

### Issue: Emails Not Sending
**Symptoms**: Email notifications fail, password resets don't work

#### Solution:
```bash
# Test email configuration
php artisan tinker
> Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')->subject('Test');
})

# Check .env email settings:
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Project Tracker"
```

## 🛠️ Quick Diagnostic Commands

### System Health Check
```bash
# Check PHP version and extensions
php -v && php -m

# Check Laravel installation
php artisan --version

# Check database connection
php artisan tinker -c "DB::connection()->getPdo()"

# Check file permissions
ls -la storage/ bootstrap/cache/

# Check web server access
curl -I http://projecttracker.test
```

### Reset Everything (Nuclear Option)
```bash
# WARNING: This will reset everything to fresh state
php artisan migrate:fresh --seed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
npm run dev
```

## 📞 Getting Help

### When to Seek Additional Support
- Multiple failed troubleshooting attempts
- Critical production issues
- Custom modification conflicts
- Security concerns

### Information to Provide
- **Error messages** (exact text)
- **Environment details** (OS, PHP version, web server)
- **Steps to reproduce** the issue
- **Log files** (relevant portions)
- **Recent changes** made to the system

### Useful Log Locations
- **Laravel**: `storage/logs/laravel.log`
- **Apache**: `/var/log/apache2/error.log`
- **Nginx**: `/var/log/nginx/error.log`
- **MySQL**: `/var/log/mysql/error.log`
- **PHP**: Check php.ini for log_errors location

---

**Troubleshooting Success** 🔧  
**Remember**: Most issues have simple solutions - start with the basics!  
**Documentation**: Check feature-specific docs for detailed information
