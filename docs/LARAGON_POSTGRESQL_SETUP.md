# PostgreSQL Setup for Laragon Development Environment

## Goal
Convert the current MySQL development environment to PostgreSQL to match the production server.

## Current Status
- **Production**: PostgreSQL database
- **Development**: MySQL (temporary setup)
- **Target**: PostgreSQL in Laragon to match production

## Step 1: Install PostgreSQL in Laragon

### Download PostgreSQL for Windows
1. Download PostgreSQL from: https://www.postgresql.org/download/windows/
2. Install PostgreSQL (recommended version 14+ for compatibility)
3. Default port: 5432
4. Set a password for the `postgres` user (remember this!)

### Alternative: Use Laragon's PostgreSQL
If Laragon supports PostgreSQL directly:
1. Open Laragon
2. Go to Menu → Services → Add Service → PostgreSQL
3. Follow Laragon's setup instructions

## Step 2: Create Database and User

```sql
-- Connect to PostgreSQL as postgres user
CREATE DATABASE projecttracker;
CREATE USER projecttracker_user WITH PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE projecttracker TO projecttracker_user;
```

## Step 3: Update Laravel Configuration

### Update .env file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=projecttracker
DB_USERNAME=projecttracker_user
DB_PASSWORD=your_secure_password
```

### Verify config/database.php has PostgreSQL config:
```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'schema' => 'public',
    'sslmode' => 'prefer',
],
```

## Step 4: Install PHP PostgreSQL Extension

### Check if pdo_pgsql is installed:
```bash
php -m | grep pgsql
```

### If not installed, add to php.ini:
```ini
extension=pdo_pgsql
extension=pgsql
```

## Step 5: Migrate Database Schema

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate:fresh

# Run seeders if available
php artisan db:seed
```

## Step 6: Data Migration from MySQL to PostgreSQL

### Option A: Laravel Migration Script
Create a custom migration command to transfer data from MySQL to PostgreSQL.

### Option B: Data Export/Import
1. Export data from current MySQL database
2. Transform data format for PostgreSQL
3. Import into new PostgreSQL database

## Step 7: Verify and Test

### Test Database Connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo()
>>> DB::select('SELECT version()')
```

### Test Application Features:
1. ProTrack DataTables functionality
2. HB837 system
3. User authentication
4. All CRUD operations

## Step 8: Update Documentation

Update all environment documentation to reflect PostgreSQL as the standard for both development and production.

---

**Next Actions:**
1. Install PostgreSQL in Laragon
2. Update .env configuration
3. Run database migrations
4. Test all functionality
5. Commit changes to git
