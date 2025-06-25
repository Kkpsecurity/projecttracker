# Test Documentation

This document describes the automated tests for the HB837 Backup/Import/Export system and related authentication features.

## Test Setup Issues & Solutions

**Current Issue:** Missing PostgreSQL PDO driver (`pdo_pgsql`) in PHP installation.

**To Fix:**
1. Edit `C:\laragon\bin\php\php-7.4.16-Win32-vc15-x64\php.ini`
2. Find and uncomment the line: `;extension=pdo_pgsql`
3. Make it: `extension=pdo_pgsql`
4. Restart your web server/Laragon

**Alternative:** Use MySQL for testing by:
1. Starting MySQL in Laragon
2. Creating a test database: `projecttracker_test`
3. Updating phpunit.xml to use MySQL connection

## Login Tests (`tests/Feature/LoginTest.php`)
- **user_can_view_login_form:** Ensures the login page is accessible.

## Import Tests (`tests/Feature/ImportTest.php`)
- **import_requires_authentication:** Import endpoint redirects unauthenticated users to login.

## How to Run Tests

**With PostgreSQL (after fixing PDO driver):**
```
php artisan test
```

**Current Status:** Tests will fail until database driver is fixed.

## Notes
- Tests use the same database as development (not isolated)
- Update or add more tests as features evolve
- Consider using database transactions for test isolation
