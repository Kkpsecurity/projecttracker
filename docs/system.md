# KKP Security Project Tracker - System Overview

## Project Structure
This Laravel application manages security projects, consultants, and administrative functions.

## Key Components

### Authentication & Users
- **User Model**: Handles authentication with AdminLTE integration
- **Database**: PostgreSQL with `fresh_` prefix for development
- **Features**: 2FA, email verification, login tracking

### Admin System  
- **User Management**: Full CRUD, bulk actions, DataTables integration
- **Site Settings**: Company info, branding, API keys, maintenance mode
- **Activity Logs**: System monitoring and audit trails

### Project Management
- **HB837 Projects**: Core project tracking functionality
- **Consultants**: Consultant information and file management
- **File Uploads**: Secure file storage and download

### Database Configuration
- **Prefix**: `fresh_` for all tables in development
- **Migration Status**: Check with `php artisan migrate:status`
- **Seeding**: Default data available via seeders

## Common Issues & Solutions

### Missing Tables
If you see "Undefined table" errors:
1. Run `php artisan migrate`
2. Check `DB_PREFIX` in `.env`
3. Verify database connection

### Authentication Issues
- Ensure `fresh_users` table exists
- Check user records with `User::count()`
- Verify email verification settings

### Settings System
- SiteSettings model provides singleton pattern
- Cached for performance
- Admin-only access required

## Development Commands
```bash
# Run migrations
php artisan migrate

# Check migration status  
php artisan migrate:status

# Clear caches
php artisan config:clear
php artisan route:clear

# Create test user
php artisan tinker --execute="App\Models\User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => bcrypt('password'), 'email_verified_at' => now()]);"
```

## AI Integration
- Error detection and documentation via `ai_commands.json`
- Automatic issue tracking in `/docs/errors/`
- Contextual fix suggestions
