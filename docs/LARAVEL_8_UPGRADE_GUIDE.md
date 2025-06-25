# Laravel 7 → 8 Upgrade Implementation Guide

**Prerequisites**: 
- ✅ PHP 8.2+ installed and running
- ✅ Environment check passes
- ✅ Application working on PHP 8.2

## 🎯 Overview
This guide implements the Laravel 7 → 8 upgrade as outlined in our upgrade plan.

## 📋 Pre-Upgrade Checklist
- [ ] PHP 8.2+ confirmed working
- [ ] Database backup exists
- [ ] Git baseline committed
- [ ] Current application tested on PHP 8.2

## 🚀 Step-by-Step Upgrade Process

### Step 1: Create Upgrade Branch
```bash
git checkout -b laravel-8-upgrade
```

### Step 2: Update composer.json Dependencies

**Current (Laravel 7):**
```json
{
    "require": {
        "php": "^7.2.5",
        "laravel/framework": "^7.0",
        "laravel/ui": "2.4"
    },
    "require-dev": {
        "facade/ignition": "^2.17",
        "nunomaduro/collision": "^4.1",
        "phpunit/phpunit": "^8.5"
    }
}
```

**Target (Laravel 8):**
```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^8.0",
        "laravel/ui": "^3.0"
    },
    "require-dev": {
        "facade/ignition": "^2.17",
        "nunomaduro/collision": "^5.0",
        "phpunit/phpunit": "^9.0"
    }
}
```

### Step 3: Update Dependencies
```bash
# Clear composer cache
composer clear-cache

# Update dependencies
composer update

# If conflicts, try:
# composer update --with-all-dependencies
```

### Step 4: Handle Breaking Changes

#### 4.1 Model Factories (if they exist)
**Laravel 7 Style:**
```php
// database/factories/UserFactory.php
$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        // ...
    ];
});
```

**Laravel 8 Style:**
```php
// database/factories/UserFactory.php
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            // ...
        ];
    }
}
```

#### 4.2 Update Seeders (if using factories)
**Laravel 7:**
```php
factory(User::class, 10)->create();
```

**Laravel 8:**
```php
User::factory(10)->create();
```

#### 4.3 Route Model Binding Updates
Check `RouteServiceProvider.php` for any custom model binding that might need updates.

### Step 5: Clear Caches and Test
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate autoload files
composer dump-autoload

# Test Laravel version
php artisan --version
# Should show Laravel Framework 8.x.x
```

### Step 6: Run Tests
```bash
# Run existing tests
php artisan test
# or
vendor/bin/phpunit

# Check for any failing tests
```

### Step 7: Test Application Functionality
1. **Basic Navigation**: Homepage, login, dashboard
2. **Authentication**: Login/logout/register
3. **CRUD Operations**: HB837, consultants, plots
4. **File Uploads**: Test document uploads
5. **Excel Operations**: Import/export functionality
6. **Database Operations**: Ensure all queries work

## 🚨 Common Laravel 8 Upgrade Issues

### Issue 1: Factory Class Not Found
**Error**: `Target class [UserFactory] does not exist`
**Fix**: 
1. Convert factories to class-based (see Step 4.1)
2. Update factory calls in seeders

### Issue 2: Deprecated Route Syntax
**Error**: Route parameter binding warnings
**Fix**: Update route model binding in `RouteServiceProvider`

### Issue 3: Middleware Changes
**Error**: Middleware not working as expected
**Fix**: Check `app/Http/Kernel.php` for middleware updates

### Issue 4: Config Cache Issues
**Error**: Configuration cached errors
**Fix**: 
```bash
php artisan config:clear
php artisan cache:clear
```

## ✅ Success Criteria

### Laravel 8 upgrade is successful when:
- [ ] `php artisan --version` shows Laravel 8.x
- [ ] No fatal errors when accessing application
- [ ] Authentication system works
- [ ] Database operations work
- [ ] File uploads work
- [ ] Excel import/export works
- [ ] No critical errors in logs

## 🔄 If Issues Occur

### Quick Rollback:
```bash
# Switch back to baseline
git checkout master
composer install
php artisan cache:clear
```

### Debug Steps:
1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Check web server logs**: Laragon logs
3. **Verify PHP version**: `php --version`
4. **Check extensions**: `php -m`
5. **Clear all caches**: Multiple artisan clear commands

## 📝 Testing Checklist

After upgrade, test these features:

### Core Application:
- [ ] Homepage loads
- [ ] User authentication (login/logout)
- [ ] Dashboard displays
- [ ] Navigation menu works

### HB837 Features:
- [ ] List HB837 projects
- [ ] Create new HB837 entry
- [ ] Edit existing entry
- [ ] File upload/download
- [ ] Excel import/export

### Admin Features:
- [ ] User management
- [ ] Consultant management
- [ ] Plot/property management
- [ ] Backup functionality

### Performance:
- [ ] Page load times reasonable
- [ ] No obvious memory leaks
- [ ] Database queries efficient

## 🎯 Next Phase
Once Laravel 8 upgrade is confirmed working:
→ **Proceed to Laravel 8 → 9 upgrade**

---
**Ready to proceed?** Ensure all checklist items are ✅ before starting.
