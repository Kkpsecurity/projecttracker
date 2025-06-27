# PostgreSQL Restoration Guide - Post-Crash Recovery

## Current Status ✅
- **PHP Extensions**: PostgreSQL extensions (pdo_pgsql, pgsql) are enabled
- **Laravel Config**: Restored to use PostgreSQL as default connection
- **Environment**: Updated .env to use PostgreSQL settings

## What We've Restored
1. **Database Configuration** (config/database.php):
   ```php
   'default' => env('DB_CONNECTION', 'pgsql'),
   ```

2. **Environment Settings** (.env):
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=projecttracker
   DB_USERNAME=postgres
   DB_PASSWORD=
   ```

## Next Steps to Complete PostgreSQL Setup

### Step 1: Start PostgreSQL Service
Since PostgreSQL is not in PATH but PHP extensions exist, PostgreSQL is likely installed but the service isn't running.

**Option A: Using Laragon Menu**
1. Open Laragon
2. Go to Menu → Services
3. Look for PostgreSQL and start it
4. Or try Right-click Laragon → Start All

**Option B: Windows Services**
1. Open Windows Services (services.msc)
2. Look for PostgreSQL service
3. Start the service if it exists

**Option C: Find PostgreSQL Installation**
```powershell
# Search for PostgreSQL installation
Get-ChildItem "C:\" -Recurse -Name "*postgres*" -Directory -ErrorAction SilentlyContinue | Select-Object -First 10

# Common locations to check:
# C:\Program Files\PostgreSQL\
# C:\PostgreSQL\
# C:\laragon\bin\postgresql\
```

### Step 2: Create Database and User
Once PostgreSQL is running, create the database:

```sql
-- Connect as postgres user (default superuser)
CREATE DATABASE projecttracker;
CREATE USER projecttracker_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE projecttracker TO projecttracker_user;
```

### Step 3: Update Password (if needed)
If you set a password for the postgres user, update .env:
```env
DB_PASSWORD=your_postgres_password
```

### Step 4: Migrate Database
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Step 5: Test Connection
```bash
php artisan tinker
>>> DB::connection()->getPdo()
>>> DB::select('SELECT version()')
```

### Step 6: Verify ProTrack DataTables
The ProTrack DataTables implementation should work seamlessly with PostgreSQL since it uses Laravel's Eloquent ORM.

## Troubleshooting

### If PostgreSQL Service Won't Start
1. Check Windows Event Viewer for PostgreSQL errors
2. Verify data directory permissions
3. Check if port 5432 is in use: `netstat -an | findstr 5432`

### If Database Connection Fails
1. Verify PostgreSQL is listening on port 5432
2. Check pg_hba.conf for authentication settings
3. Ensure database 'projecttracker' exists

### If Migration Issues
PostgreSQL is stricter than MySQL, so some migrations might need adjustments:
- Use `::text` for text casting
- Boolean fields work differently
- Case-sensitive table/column names

## Data Migration Options

### Option A: Fresh Start
- Run migrations fresh
- Re-seed with test data
- Import production data later

### Option B: MySQL to PostgreSQL Data Transfer
- Export current MySQL data
- Transform for PostgreSQL compatibility
- Import into PostgreSQL

---

**Current State**: PostgreSQL configuration restored, service needs to be started
**Next Action**: Start PostgreSQL service in Laragon and test connection
