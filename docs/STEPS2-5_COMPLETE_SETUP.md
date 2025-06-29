# 🚀 Steps 2-5: Complete Fresh Laravel + AdminLTE Setup

**Goal**: Create minimal but complete Laravel installation with AdminLTE and authentication ready for migration.

---

## 📋 **Complete Setup Commands**

### **Step 2-3: Fresh Laravel + Database**
```powershell
# Navigate to Laragon directory
cd c:\laragon\www

# Create fresh Laravel project
composer create-project laravel/laravel projecttracker_fresh

# Navigate to new project
cd projecttracker_fresh

# Generate application key
php artisan key:generate
```

### **Step 4: Install Core Dependencies**
```powershell
# Install Laravel UI for authentication
composer require laravel/ui

# Install AdminLTE
composer require jeroennoten/laravel-adminlte

# Install packages from original project
composer require barryvdh/laravel-dompdf
composer require laracasts/flash
composer require maatwebsite/excel
```

### **Step 5: Setup Authentication & AdminLTE**
```powershell
# Generate authentication scaffolding
php artisan ui bootstrap --auth

# Install AdminLTE with full setup
php artisan adminlte:install --type=enhanced --with=auth_views

# Publish AdminLTE configuration
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\AdminLteServiceProvider"

# Run migrations to create users table
php artisan migrate
```

### **Step 6: Configure Database**
Edit `.env` file:
```env
APP_NAME=ProjectTracker
APP_URL=http://projecttracker_fresh.test

DB_CONNECTION=pgsql
DB_HOST=criustemp.hq.cisadmin.com
DB_PORT=5432
DB_DATABASE=projecttracker_fresh
DB_USERNAME=projecttracker
DB_PASSWORD=>po/xDG3~.07a?Xd

SESSION_DRIVER=file
SESSION_LIFETIME=480
SESSION_DOMAIN=.projecttracker_fresh.test
```

### **Step 7: Test Basic Setup**
```powershell
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Test migrations (create fresh database first)
php artisan migrate:fresh

# Create test user
php artisan tinker
# In tinker:
User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password123')]);
exit

# Start server
php artisan serve --host=127.0.0.1 --port=8000
```

---

## 🎯 **Expected Results After Setup**

### **✅ URLs to Test:**
- `http://127.0.0.1:8000` - Laravel welcome page
- `http://127.0.0.1:8000/login` - AdminLTE login page
- `http://127.0.0.1:8000/register` - AdminLTE register page
- `http://127.0.0.1:8000/home` - AdminLTE dashboard (after login)

### **✅ Login Test:**
- Email: `admin@test.com`
- Password: `password123`
- **Should work without CSRF errors**

### **✅ Features Working:**
- Beautiful AdminLTE login/dashboard
- CSRF protection (no 419 errors)
- User authentication
- Database connectivity
- Session management

---

## 📁 **Directory Structure After Setup**
```
c:\laragon\www\
├── projecttracker\              # Original (with issues)
├── projecttracker_fresh\        # New working installation
│   ├── app/
│   ├── config/adminlte.php     # AdminLTE configuration
│   ├── resources/views/        # AdminLTE views
│   └── .env                    # Fresh configuration
└── projecttracker_backups/     # Backup
```

---

## 🔧 **Key Configuration Files**

### **config/adminlte.php** - Main AdminLTE settings
### **.env** - Database and session configuration  
### **routes/web.php** - Authentication routes (auto-generated)

---

## ⚠️ **Important Notes**

1. **Create `projecttracker_fresh` database** in PostgreSQL before running migrations
2. **Test login thoroughly** - this should NOT have CSRF issues
3. **Keep original project untouched** during this setup
4. **Document any issues** encountered during setup

---

## 🎯 **Success Criteria**

- [ ] Fresh Laravel project created
- [ ] AdminLTE installed and configured
- [ ] Database connected (new database)
- [ ] Authentication working
- [ ] **CSRF tokens working** (no 419 errors)
- [ ] AdminLTE dashboard accessible
- [ ] Test user can login successfully

---

## 🚀 **After Setup Complete**

**PAUSE HERE** and test everything thoroughly before proceeding to data migration. 

**Next Phase**: Migrate controllers, models, and data from original project.

---

**Ready to run these commands?** Execute them step by step and report any issues.
