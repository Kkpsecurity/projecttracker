# Installation Guide

**Project Tracker** - Security Consulting Management System  
**Target Environment**: Windows with Laragon (Recommended)  
**Alternative**: Linux/macOS with similar stack

## 🎯 System Requirements

### Minimum Requirements
- **PHP**: 8.3 or higher
- **MySQL**: 8.0 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 18.0 or higher (for asset compilation)
- **Web Server**: Apache 2.4+ or Nginx 1.18+

### Recommended Environment
- **Laragon**: Full development environment for Windows
- **RAM**: 4GB minimum, 8GB recommended
- **Storage**: 2GB free space for application and dependencies
- **PHP Extensions**: Required Laravel extensions enabled

## 🚀 Quick Installation (Laragon - Recommended)

### Step 1: Environment Setup
```bash
# Download and install Laragon
# Visit: https://laragon.org/download/
# Install with default settings including:
# - Apache 2.4
# - MySQL 8.0
# - PHP 8.3
# - Node.js 20+
```

### Step 2: Project Setup
```bash
# Navigate to Laragon web directory
cd C:\laragon\www

# Clone or extract project
git clone <repository-url> projecttracker
# OR extract ZIP file to projecttracker folder

# Navigate to project directory
cd projecttracker
```

### Step 3: Dependencies Installation
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Compile assets
npm run dev
```

### Step 4: Environment Configuration
```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Database Setup
```bash
# Start Laragon services
# Open Laragon -> Start All

# Create database (via HeidiSQL or phpMyAdmin)
# Database name: projecttracker

# Update .env file with database credentials:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projecttracker
DB_USERNAME=root
DB_PASSWORD=

# Run migrations and seed data
php artisan migrate:fresh --seed
```

### Step 6: Final Configuration
```bash
# Set up virtual host in Laragon
# Laragon -> Apache -> sites-enabled -> projecttracker.conf
# OR use auto virtual hosts (projecttracker.test)

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Set permissions (if needed)
# Ensure storage and bootstrap/cache are writable
```

## 🔧 Manual Installation (Alternative)

### Prerequisites Installation

#### PHP 8.3+ Installation
```bash
# Windows (download from php.net)
# Enable required extensions in php.ini:
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=tokenizer
extension=xml
extension=curl
extension=zip
```

#### MySQL 8.0+ Installation
```bash
# Download MySQL Community Server from mysql.com
# Install with default settings
# Create root user with password (optional)
# Start MySQL service
```

#### Composer Installation
```bash
# Download from getcomposer.org
# Install globally for command line access
# Verify: composer --version
```

#### Node.js Installation
```bash
# Download LTS version from nodejs.org
# Install with default settings
# Verify: node --version && npm --version
```

### Application Setup

#### 1. Project Download
```bash
# Download project files
# Extract to web server directory
# Example: C:\xampp\htdocs\projecttracker
```

#### 2. Dependencies
```bash
cd /path/to/projecttracker

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm ci --production

# Compile assets for production
npm run production
```

#### 3. Environment Setup
```bash
# Copy and configure environment
cp .env.example .env

# Edit .env file with your settings:
APP_NAME="Project Tracker"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projecttracker
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

#### 4. Application Key
```bash
# Generate unique application key
php artisan key:generate
```

#### 5. Database Migration
```bash
# Create database first (via phpMyAdmin or command line)
mysql -u root -p -e "CREATE DATABASE projecttracker"

# Run migrations
php artisan migrate

# Seed with sample data (optional)
php artisan db:seed
```

#### 6. Storage Setup
```bash
# Create symbolic link for storage
php artisan storage:link

# Set proper permissions (Linux/macOS)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🌐 Web Server Configuration

### Apache Configuration
```apache
# Virtual Host Configuration
<VirtualHost *:80>
    ServerName projecttracker.local
    DocumentRoot "C:/laragon/www/projecttracker/public"
    
    <Directory "C:/laragon/www/projecttracker/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/projecttracker-error.log"
    CustomLog "logs/projecttracker-access.log" combined
</VirtualHost>
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name projecttracker.local;
    root /var/www/projecttracker/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## 🔒 Security Configuration

### Production Security Settings
```bash
# .env production settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database security
DB_HOST=localhost  # Use localhost instead of 127.0.0.1
DB_PASSWORD=strong_random_password

# Session security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Additional security headers (in .htaccess or server config)
```

### File Permissions
```bash
# Recommended file permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

## 🧪 Installation Verification

### Health Check Commands
```bash
# Verify PHP version and extensions
php -v
php -m | grep -E "(mysql|pdo|openssl|mbstring|tokenizer|xml)"

# Verify database connection
php artisan tinker
> DB::connection()->getPdo()

# Verify web server access
curl -I http://projecttracker.test
# Should return 200 OK

# Verify admin access
# Visit: http://projecttracker.test/admin/home
# Should redirect to login if not authenticated
```

### Test Data Verification
```bash
# Check if seeders ran successfully
php artisan tinker
> User::count()          # Should show seeded users
> Client::count()        # Should show seeded clients
> SiteSettings::count()  # Should show default settings
```

## 🚨 Troubleshooting Common Issues

### Permission Issues (Linux/macOS)
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# SELinux issues (CentOS/RHEL)
sudo setsebool -P httpd_can_network_connect 1
sudo chcon -R -t httpd_exec_t /var/www/projecttracker
```

### Database Connection Issues
```bash
# Test MySQL connection manually
mysql -h 127.0.0.1 -u root -p projecttracker

# Check MySQL service status
# Windows: services.msc -> MySQL80
# Linux: systemctl status mysql
```

### Asset Compilation Issues
```bash
# Clear npm cache
npm cache clean --force

# Reinstall node modules
rm -rf node_modules package-lock.json
npm install

# Compile assets with verbose output
npm run dev --verbose
```

### Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate optimized files
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📋 Post-Installation Checklist

### ✅ Required Verifications
- [ ] Application loads without errors
- [ ] Database connection successful
- [ ] Admin login functional
- [ ] File uploads working (test logo upload)
- [ ] Email configuration tested
- [ ] All main features accessible
- [ ] Mobile responsiveness verified
- [ ] Browser compatibility checked

### ✅ Optional Configurations
- [ ] SSL certificate installed
- [ ] Backup system configured
- [ ] Monitoring tools setup
- [ ] Performance optimization applied
- [ ] Security hardening completed
- [ ] Documentation reviewed

## 🎉 Success!

If all steps completed successfully, you should have:

- ✅ **Working Application** at your configured URL
- ✅ **Admin Access** via `/admin/home`
- ✅ **Database** with sample data
- ✅ **Modern Interface** with AdminLTE
- ✅ **Mobile Responsive** design
- ✅ **Production Ready** configuration

### Next Steps
1. **Review** the [Configuration Guide](configuration.md)
2. **Follow** the [First Steps Guide](first-steps.md)
3. **Explore** the [Features Documentation](../features/)
4. **Set up** regular backups and monitoring

---

**Installation Complete!** 🎉  
**Support**: Check [troubleshooting guide](../troubleshooting/common-issues.md) for issues  
**Updates**: Follow the documentation for future updates
