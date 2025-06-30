# Laravel Live Server Deployment Instructions

## 🚨 CRITICAL ISSUE RESOLUTION

The error you're seeing:
```
file_put_contents(C:\laragon\www\projecttracker_fresh\storage\framework/sessions/...): Failed to open stream: No such file or directory
```

This indicates the application is using **local development paths** on the live server. Here's how to fix it:

## 🚨 **EXCEL PACKAGE ERROR FIX**

If you see this error:
```
Class "PhpOffice\PhpSpreadsheet\Reader\Csv" not found
Package phpoffice/phpexcel is abandoned, you should avoid using it. Use phpoffice/phpspreadsheet instead.
```

**SOLUTION 1: Install PHP Zip Extension (Recommended)**
```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install php-zip php-xml php-gd
sudo systemctl restart apache2

# CentOS/RHEL
sudo yum install php-zip php-xml php-gd
sudo systemctl restart httpd
```

**SOLUTION 2: Install without Zip Extension**
```bash
composer install --ignore-platform-req=ext-zip --no-dev --optimize-autoloader
php artisan package:discover --ansi
```

## 📋 DEPLOYMENT STEPS

### 1. **Upload Files to Live Server**
- Upload the entire project to your live server
- Ensure all files are in the correct web directory (usually `public_html` or `www`)

### 2. **Create Required Directories**
On your live server, run this script to create all necessary directories:

```bash
php setup_server_deployment.php
```

Or manually create these directories with proper permissions:
```bash
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 3. **Configure Environment**
- Copy `.env.production` to `.env` on your live server
- Update the `.env` file with your server-specific settings:
  - Set `APP_ENV=production`
  - Set `APP_DEBUG=false`
  - Update `APP_URL` to your domain
  - Verify database credentials

### 4. **Clear All Caches** ⚠️ **CRITICAL**
Run these commands on your live server:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
```

### 5. **Set Proper Permissions**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

## 🔧 **QUICK FIX SCRIPT**

Run this on your live server to fix the issue immediately:

```bash
# Create directories
mkdir -p storage/framework/sessions
mkdir -p storage/framework/cache
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Cache config for production
php artisan config:cache
```

## 🚨 **COMMON ISSUES & SOLUTIONS**

### Issue: Local Windows paths showing on Linux server
**Solution**: Clear config cache with `php artisan config:clear`

### Issue: Permission denied errors
**Solution**: 
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Issue: Sessions not working
**Solution**: 
```bash
mkdir -p storage/framework/sessions
chmod 755 storage/framework/sessions
```

## 🚨 **DNS/DATABASE CACHE ERROR FIX**

If you see this error:
```
SQLSTATE[08006] [7] could not translate host name "criustemp.hq.cisadmin.com" to address: Temporary failure in name resolution
```

**IMMEDIATE SOLUTION:**
```bash
# On your server, run this to fix immediately:
bash fix_dns_cache_issue.sh
```

**Manual Fix:**
```bash
# Update .env file to use file cache instead of database cache:
sed -i 's/CACHE_STORE=database/CACHE_STORE=file/g' .env
sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=sync/g' .env

# Clear caches safely:
php artisan config:clear
php artisan cache:clear
```

**Root Cause:** The application tries to clear database cache but can't resolve the database hostname.

## 📁 **REQUIRED DIRECTORY STRUCTURE**
```
your-app/
├── storage/
│   ├── app/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/     ← MUST EXIST
│   │   └── views/
│   └── logs/
├── bootstrap/
│   └── cache/           ← MUST EXIST
└── public/
    └── index.php        ← Entry point
```

## ✅ **VERIFICATION**

After deployment, verify:
1. All directories exist with proper permissions
2. `.env` file has production settings
3. No cached local paths remain
4. Application loads without errors

## 🔄 **RE-DEPLOYMENT**

When uploading updates:
1. Upload new files
2. Run `php artisan config:clear`
3. Run `php artisan config:cache`
4. Test the application
