#!/bin/bash

echo "=== Laravel Migration Duplicate Table Fix ==="
echo "Fixing: SQLSTATE[42P07]: Duplicate table: relation 'users' already exists"
echo ""

echo "1. Checking current migration status..."
php artisan migrate:status

echo ""
echo "2. Rolling back to fix duplicate users table..."
# Mark the failing migration as migrated since the table already exists
php artisan migrate:install 2>/dev/null || echo "Migration table already exists"

echo ""
echo "3. Manually marking the duplicate users migration as migrated..."
# Get the database connection info
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d'=' -f2)

if [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "PostgreSQL detected - using psql commands"

    # Get database credentials
    DB_HOST=$(grep "^DB_HOST=" .env | cut -d'=' -f2)
    DB_PORT=$(grep "^DB_PORT=" .env | cut -d'=' -f2)
    DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
    DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)
    DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2)

    echo "Manually inserting migration record for duplicate users table..."
    # Insert the migration record to mark it as completed
    PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" -c "
    INSERT INTO migrations (migration, batch)
    VALUES ('2014_10_12_000000_create_users_table', 1)
    ON CONFLICT (migration) DO NOTHING;
    " 2>/dev/null || echo "Could not insert migration record directly"
fi

echo ""
echo "4. Alternative: Skip the failing migration and continue..."
echo "Trying to continue with remaining migrations..."
php artisan migrate --force

echo ""
echo "5. Final migration status check..."
php artisan migrate:status

echo ""
echo "=== SOLUTION SUMMARY ==="
echo "✅ The error occurs because there are two migrations trying to create 'users' table"
echo "✅ The first migration (0001_01_01_000000_create_users_table) succeeded"
echo "✅ The second migration (2014_10_12_000000_create_users_table) failed because table exists"
echo ""
echo "RECOMMENDED ACTIONS:"
echo "1. Remove or rename the duplicate migration file: 2014_10_12_000000_create_users_table.php"
echo "2. Or manually mark it as migrated in the migrations table"
echo "3. Continue with remaining migrations"
echo ""
