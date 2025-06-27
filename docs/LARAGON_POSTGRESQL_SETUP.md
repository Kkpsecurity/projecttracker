# Enable PostgreSQL Support in Laragon

## Current Issue
Your PHP installation (8.4.8) doesn't have the PostgreSQL extension enabled, which is why you're getting the "could not find driver" error.

## Solution: Enable PostgreSQL Extension in Laragon

### Step 1: Enable PostgreSQL Extension
1. **Right-click on Laragon** in the system tray
2. Go to **PHP** → **Extensions**
3. Check (enable) **pdo_pgsql** and **pgsql**
4. **Restart Laragon** services

### Step 2: Alternative Manual Method
If the menu option isn't available:

1. Navigate to your Laragon PHP directory, typically:
   ```
   C:\laragon\bin\php\php-8.4.8\
   ```

2. Open `php.ini` file in a text editor

3. Find these lines and uncomment them (remove the semicolon):
   ```ini
   ;extension=pdo_pgsql
   ;extension=pgsql
   ```
   
   Change to:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```

4. Save the file and restart Laragon

### Step 3: Verify Installation
After restarting, run:
```bash
php -m | findstr pgsql
```

You should see:
```
pdo_pgsql
pgsql
```

### Step 4: Test Database Connection
Once the extensions are loaded:
```bash
php artisan migrate:status
```

## Alternative: Use MySQL Temporarily
If you prefer to stick with MySQL for now:

1. Update `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=projecttracker
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Create the MySQL database:
   ```sql
   CREATE DATABASE projecttracker;
   ```

## Recommendation
For production consistency, I recommend enabling PostgreSQL support in Laragon since your live server uses PostgreSQL.
