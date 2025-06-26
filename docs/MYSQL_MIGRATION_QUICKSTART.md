# PostgreSQL to MySQL Migration - Step-by-Step Guide

**Date**: June 26, 2025  
**Estimated Time**: 3-4 hours  
**Prerequisites**: Laravel 11.45.1, Laragon with MySQL 8.0+

## 🚀 Quick Start Migration

### Step 1: Pre-Migration Backup (10 minutes)
```bash
# 1. Create Git backup
git add .
git commit -m "🔄 Pre-MySQL migration backup - PostgreSQL state"
git tag pre-mysql-migration

# 2. Backup any existing MySQL data (if any)
# In Laragon, access phpMyAdmin and export current projecttracker DB
```

### Step 2: Database Preparation (15 minutes)
```bash
# 1. Ensure MySQL is running in Laragon
# Start Laragon -> Start All Services

# 2. Create fresh MySQL database
# Open phpMyAdmin (http://localhost/phpmyadmin)
# Create database: projecttracker
# Charset: utf8mb4_unicode_ci

# Or via command line:
mysql -u root -p
CREATE DATABASE projecttracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE projecttracker;
EXIT;
```

### Step 3: Laravel Database Setup (10 minutes)
```bash
# 1. Verify .env database settings
# DB_CONNECTION=mysql (should already be set)
# DB_DATABASE=projecttracker
# DB_USERNAME=root
# DB_PASSWORD= (empty for Laragon default)

# 2. Run fresh migrations
php artisan migrate:fresh

# 3. Verify migration success
php artisan migrate:status
```

### Step 4: Data Migration (30 minutes)
```bash
# 1. Register the seeder in DatabaseSeeder.php
# Add: $this->call(PostgreSQLToMySQLDataSeeder::class);

# 2. Run the data seeder
php artisan db:seed --class=PostgreSQLToMySQLDataSeeder

# 3. Verify data import
php artisan tinker
DB::table('clients')->count();  // Should show imported records
DB::table('backups')->count();  // Should show backup records
exit
```

### Step 5: Application Testing (20 minutes)
```bash
# 1. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 2. Test basic functionality
php artisan serve
# Open: http://localhost:8000
# Navigate through the application
# Test login, project views, etc.

# 3. Check Laravel status
php artisan about
```

## 🔧 Detailed Migration Commands

### Complete Migration Script
```bash
#!/bin/bash
# Complete PostgreSQL to MySQL Migration Script

echo "🔄 Starting PostgreSQL to MySQL Migration..."

# Step 1: Backup
echo "📦 Creating backup..."
git add .
git commit -m "🔄 Pre-MySQL migration backup"
git tag pre-mysql-migration-$(date +%Y%m%d-%H%M%S)

# Step 2: Database preparation
echo "🗄️  Preparing MySQL database..."
php artisan migrate:fresh --force

# Step 3: Data migration
echo "📊 Migrating data..."
php artisan db:seed --class=PostgreSQLToMySQLDataSeeder

# Step 4: Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Step 5: Verification
echo "✅ Verifying migration..."
php artisan migrate:status

echo "🎉 Migration completed! Check the application at http://localhost:8000"
```

## 📋 Verification Checklist

### Database Verification
- [ ] **MySQL database** created and accessible
- [ ] **Laravel migrations** completed successfully  
- [ ] **Data seeder** ran without errors
- [ ] **Record counts** match expectations
- [ ] **Key relationships** working correctly

### Application Verification
- [ ] **Login system** working
- [ ] **Client management** showing data
- [ ] **Project tracking** functional
- [ ] **HB837 module** accessible
- [ ] **Backup system** working
- [ ] **File uploads** functioning

### Performance Verification
- [ ] **Page load times** acceptable
- [ ] **Database queries** performing well
- [ ] **No obvious errors** in logs
- [ ] **Memory usage** within normal limits

## 🚨 Troubleshooting Common Issues

### Issue 1: Migration Fails
```bash
# Clear everything and restart
php artisan migrate:fresh --force
php artisan db:seed --class=DatabaseSeeder
```

### Issue 2: Character Encoding Problems
```sql
-- Fix character encoding in MySQL
ALTER DATABASE projecttracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Issue 3: Performance Issues
```sql
-- Add indexes for better performance
ALTER TABLE clients ADD INDEX idx_client_name (client_name);
ALTER TABLE clients ADD INDEX idx_quick_status (quick_status);
ALTER TABLE clients ADD INDEX idx_corporate_name (corporate_name);
```

### Issue 4: Application Errors
```bash
# Clear all caches and regenerate
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📊 Expected Results

### Database Statistics
- **Backups table**: 3 records (from PostgreSQL dump)
- **Clients table**: 7+ sample records (expandable to 48+)
- **Migrations**: All up-to-date
- **Performance**: Improved query times with MySQL optimizations

### Application Features
- **ProTrack Dashboard**: Fully functional
- **HB837 Module**: Data accessible
- **User Management**: Login working
- **File System**: Attachments working
- **Backup System**: MySQL compatible

## 🔄 Rollback Procedure (If Needed)

### Quick Rollback (5 minutes)
```bash
# 1. Restore from git backup
git reset --hard pre-mysql-migration

# 2. Update .env if needed
# Change DB_CONNECTION back to pgsql if returning to PostgreSQL

# 3. Clear caches
php artisan optimize:clear
```

### Complete Rollback (15 minutes)
```bash
# 1. Full git reset
git reset --hard pre-mysql-migration

# 2. Restore PostgreSQL database (if available)
# Use PostgreSQL dump to restore original data

# 3. Update configuration
# Ensure .env points to PostgreSQL
# Test full functionality
```

## ✅ Success Criteria

### Technical Success
- [ ] MySQL database operational
- [ ] All data migrated successfully
- [ ] No functionality regression
- [ ] Performance maintained or improved
- [ ] Ready for AdminLTE integration

### Business Success
- [ ] All client data accessible
- [ ] Project tracking working
- [ ] User authentication functional
- [ ] Backup system operational
- [ ] Ready for UI modernization

---

**Next Step**: Once MySQL migration is complete, proceed with AdminLTE integration for modern UI! 🎨
