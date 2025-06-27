@echo off
echo ===========================================
echo  Test Database Setup for DataTables Testing
echo ===========================================
echo.

set PGPASSWORD=>po/xDG3~.07a?Xd
set DB_HOST=criustemp.hq.cisadmin.com
set DB_PORT=5432
set DB_USER=projecttracker
set PROD_DB=projecttracker
set TEST_DB=projecttracker_test

echo 📊 Checking if test database exists...
psql -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d postgres -c "SELECT 1 FROM pg_database WHERE datname = '%TEST_DB%';" -t | findstr /r "^[ ]*1[ ]*$" >nul

if errorlevel 1 (
    echo 🔨 Creating test database '%TEST_DB%'...
    psql -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d postgres -c "CREATE DATABASE %TEST_DB%;"
    if errorlevel 1 (
        echo ❌ Failed to create test database
        exit /b 1
    )
    echo ✅ Test database created successfully!
) else (
    echo ℹ️  Test database already exists.
    echo 🗑️  Dropping existing test database to ensure clean copy...
    psql -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d postgres -c "DROP DATABASE IF EXISTS %TEST_DB%;"
    echo 🔨 Creating fresh test database...
    psql -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d postgres -c "CREATE DATABASE %TEST_DB%;"
    if errorlevel 1 (
        echo ❌ Failed to create test database
        exit /b 1
    )
    echo ✅ Fresh test database created!
)

echo.
echo 📋 Copying database schema and data from production...
echo 🔄 This may take a few minutes for large datasets...

pg_dump -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d %PROD_DB% --no-owner --no-privileges > temp_backup.sql

if errorlevel 1 (
    echo ❌ Failed to create database backup
    exit /b 1
)

echo 📥 Restoring data to test database...
psql -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d %TEST_DB% < temp_backup.sql

if errorlevel 1 (
    echo ❌ Failed to restore database backup
    del temp_backup.sql
    exit /b 1
)

echo 🧹 Cleaning up temporary files...
del temp_backup.sql

echo.
echo 🔍 Verifying test database...
for /f %%i in ('psql -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d %TEST_DB% -c "SELECT COUNT(*) FROM hb837;" -t') do set RECORD_COUNT=%%i

echo 📊 Test database contains %RECORD_COUNT% HB837 records
echo.
echo 🎉 Test database setup completed successfully!
echo 📊 Database: %TEST_DB%
echo 🔗 Connection: %DB_HOST%:%DB_PORT%
echo 👤 Username: %DB_USER%
echo.
echo ⚠️  IMPORTANT: .env file now points to TEST database
echo 📝 Remember to switch back to production when done testing
echo.
echo ✨ Ready for DataTables testing!
pause
