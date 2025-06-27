# PostgreSQL Database Configuration

## Overview
The ProjectTracker application now uses PostgreSQL as the default database to match the production environment.

## Requirements
- PostgreSQL 15+ (recommended)
- PHP 8.3 with `pgsql` extension
- Laravel 11

## Local Development Setup

### 1. Install PostgreSQL
For Windows/Laragon:
1. Download PostgreSQL from https://www.postgresql.org/download/windows/
2. Install with default settings (port 5432)
3. Set a password for the `postgres` user

### 2. Create Database
```sql
CREATE DATABASE projecttracker;
```

### 3. Configure Environment
Update your `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=projecttracker
DB_USERNAME=postgres
DB_PASSWORD=your_password_here
```

### 4. Run Migrations
```bash
php artisan migrate
php artisan db:seed
```

## Production Environment
The application is configured to use PostgreSQL in production. Ensure your production server has:
- PostgreSQL 15+
- PHP 8.3 with `pgsql` extension
- Proper database credentials configured

## CI/CD Pipeline
GitHub Actions now uses PostgreSQL 15 for testing to match production:
- Runs automated tests against PostgreSQL
- Validates migrations and seeders
- Ensures code quality with PostgreSQL-specific features

## Migration from MySQL
If migrating from MySQL, use the existing migration scripts in `database/migrations/` which are compatible with both databases.

## Troubleshooting
- Ensure PostgreSQL service is running
- Check port 5432 is not blocked by firewall
- Verify `pgsql` PHP extension is installed: `php -m | grep pgsql`
- Check Laravel can connect: `php artisan tinker` then `DB::connection()->getPdo()`
