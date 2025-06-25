# Laragon PHP Upgrade Verification

**Instructions**: Run these commands AFTER upgrading PHP in Laragon

## Step 1: Verify PHP Version
```bash
php --version
```
**Expected**: Should show PHP 8.2.x or higher

## Step 2: Check SQLite Extension
```bash
php -m | findstr -i sqlite
```
**Expected**: Should show `pdo_sqlite` and `sqlite3`

## Step 3: Run Environment Check
```bash
php environment_check.php
```
**Expected**: Should show "✅ ENVIRONMENT: READY for Laravel upgrade!"

## Step 4: Test Laravel
```bash
php artisan --version
```
**Expected**: Should show "Laravel Framework 7.30.7" without errors

## Step 5: Test Application
1. Make sure Laragon services are running
2. Visit your local URL (usually `http://projecttracker.test`)
3. Try logging in
4. Check for any PHP 8.2 compatibility warnings

## If You See Errors:

### Common Issues:
1. **"Call to undefined function"** → Missing PHP extension
2. **"Access denied"** → Permission issues
3. **"Connection refused"** → Laragon services not running

### Quick Fixes:
1. **Restart Laragon services** (Stop All → Start All)
2. **Check PHP extensions** in Laragon Menu → PHP → Extensions
3. **Clear Laravel caches**: `php artisan cache:clear`

## When Ready:
✅ All checks pass → **Proceed to Laravel 8 upgrade**
❌ Issues found → **Fix in Laragon first**

---
**Next**: Run `php environment_check.php` to confirm readiness
