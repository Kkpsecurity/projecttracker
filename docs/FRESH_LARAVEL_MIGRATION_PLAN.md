# 🚀 Fresh Laravel Migration Plan - 12 Steps

**Project**: ProjectTracker Laravel Application
**Date**: June 27, 2025
**Goal**: Migrate from problematic CSRF setup to fresh Laravel installation

---

## 📋 **Step 1: Assessment & Backup**
- [ ] Document current database structure
- [ ] Export current database with `pg_dump`
- [ ] Backup current project folder (ZIP/RAR)
- [ ] Document all custom configurations
- [ ] List all installed packages from `composer.json`
- [ ] Screenshot current application features

**Files to backup:**
- Database dump
- `.env` file
- Custom controllers, models, views
- Public assets (uploads, custom CSS/JS)

---

## 📋 **Step 2: Fresh Laravel Installation**
- [ ] Create new Laravel project: `composer create-project laravel/laravel projecttracker_fresh`
- [ ] Move to new directory
- [ ] Run `php artisan key:generate`
- [ ] Test basic Laravel welcome page works
- [ ] Confirm CSRF tokens work on basic forms

**Location**: `c:\laragon\www\projecttracker_fresh`

---

## 📋 **Step 3: Database Setup**
- [ ] Configure PostgreSQL connection in new `.env`
- [ ] Create fresh database: `projecttracker_fresh`
- [ ] Test database connection: `php artisan migrate`
- [ ] Import old database structure (without data first)
- [ ] Verify database connectivity

**Database**: `projecttracker_fresh`

---

## 📋 **Step 4: Basic Authentication Setup**
- [ ] Install Laravel UI: `composer require laravel/ui`
- [ ] Generate auth scaffolding: `php artisan ui bootstrap --auth`
- [ ] Run migrations: `php artisan migrate`
- [ ] Test basic login/register functionality
- [ ] Confirm CSRF works on auth forms

**Goal**: Working authentication without CSRF issues

---

## 📋 **Step 5: AdminLTE Installation**
- [ ] Install AdminLTE: `composer require jeroennoten/laravel-adminlte`
- [ ] Publish AdminLTE resources: `php artisan adminlte:install --type=enhanced`
- [ ] Configure AdminLTE settings in `config/adminlte.php`
- [ ] Test AdminLTE login page
- [ ] Verify AdminLTE dashboard loads

**Goal**: Beautiful AdminLTE interface working

---

## 📋 **Step 6: Route Structure Migration**
- [ ] Copy route structure from `routes/web.php`
- [ ] Implement admin prefix routes
- [ ] Add password reset routes
- [ ] Test all route definitions
- [ ] Confirm route names match old system

**Focus**: Route compatibility

---

## 📋 **Step 7: Models & Database Migration**
- [ ] Copy all models from `app/Models/`
- [ ] Copy migrations from `database/migrations/`
- [ ] Update model relationships
- [ ] Run migrations: `php artisan migrate:fresh`
- [ ] Test model functionality with tinker

**Models to migrate**:
- User
- HB837
- Consultant
- Plot
- Any other custom models

---

## 📋 **Step 8: Controllers Migration**
- [ ] Copy controllers one by one:
  - [ ] HomeController
  - [ ] LoginController (if customized)
  - [ ] HB837Controller
  - [ ] UserController
  - [ ] GoogleMapsController
  - [ ] BackupDBController
- [ ] Update namespaces if needed
- [ ] Test each controller's basic functionality

**Goal**: All business logic migrated

---

## 📋 **Step 9: Views Migration**
- [ ] Copy blade templates:
  - [ ] Admin dashboard views
  - [ ] HB837 management views
  - [ ] User management views
  - [ ] Custom layouts (if not using AdminLTE)
- [ ] Update AdminLTE view extensions
- [ ] Test view rendering

**Goal**: All pages display correctly

---

## 📋 **Step 10: Dependencies & Packages**
- [ ] Install required packages:
  - [ ] `maatwebsite/excel` (Excel import/export)
  - [ ] `barryvdh/laravel-dompdf` (PDF generation)
  - [ ] `laracasts/flash` (Flash messages)
  - [ ] Any other custom packages
- [ ] Copy custom JavaScript/CSS files
- [ ] Update asset references

**Goal**: All functionality restored

---

## 📋 **Step 11: Data Migration**
- [ ] Export data from old database
- [ ] Import data to new database
- [ ] Verify data integrity
- [ ] Test data relationships
- [ ] Create test user account
- [ ] Verify login works with real data

**Goal**: All data successfully migrated

---

## 📋 **Step 12: Testing & Go-Live**
- [ ] Full application testing:
  - [ ] Login/logout functionality
  - [ ] CSRF protection working
  - [ ] All CRUD operations
  - [ ] File uploads/downloads
  - [ ] Excel import/export
  - [ ] PDF generation
  - [ ] Google Maps integration
- [ ] Performance testing
- [ ] Backup verification
- [ ] Switch Laragon virtual host
- [ ] Archive old project

**Goal**: Production-ready application

---

## 🎯 **Success Criteria**
- ✅ No CSRF "Session expired" errors
- ✅ All original functionality preserved
- ✅ AdminLTE interface working perfectly
- ✅ Database operations functioning
- ✅ File operations working
- ✅ Performance equal or better than original

---

## 📁 **Directory Structure**
```
c:\laragon\www\
├── projecttracker\           # Original (backup)
├── projecttracker_fresh\     # New Laravel installation
└── projecttracker_backup\    # Archive of original
```

---

## ⚠️ **Risk Mitigation**
- Keep original project folder until migration is 100% complete
- Test each step thoroughly before proceeding
- Maintain database backups at each major step
- Document any issues encountered during migration

---

## 📞 **Rollback Plan**
If migration fails:
1. Restore original `projecttracker` folder
2. Restore original database
3. Continue troubleshooting CSRF issue in original project

---

**Next Step**: Begin with Step 1 - Assessment & Backup
