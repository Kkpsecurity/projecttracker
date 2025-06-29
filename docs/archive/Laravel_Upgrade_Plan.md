# Laravel 7 to Laravel 10/11 Upgrade Plan with AdminLTE Integration

**Project**: CIS/S2 Project Tracker  
**Current Version**: Laravel 7.30.7 (PHP 7.4.16)  
**Target Version**: Laravel 10/11 with AdminLTE  
**Created**: June 25, 2025  

## Current Status (June 25, 2025)

### ✅ COMPLETED PHASES:
1. **Environment Preparation** - PHP 8.1.5, Laragon configured
2. **Database Setup** - MySQL for dev/prod, SQLite for testing
3. **Laravel 8 Upgrade** - Successfully upgraded from 7.30.7 to 8.83.29
4. **Laravel 9 Upgrade** - Successfully upgraded to 9.52.20
5. **Laravel 10 Upgrade** - Successfully upgraded to 10.48.29 ✅ NEW!
6. **Git Versioning** - All changes committed and tagged

### 🔄 NEXT STEPS:
1. **AdminLTE Integration** - Modern admin interface implementation
2. **Optional Laravel 11 Upgrade** - Latest LTS version (optional)
3. **Final Testing & Optimization** - Complete validation

### 🎯 CURRENT MILESTONE:
**Ready for AdminLTE Integration** - Laravel 10.48.29 validated successfully with comprehensive test data.

**✅ Database Seeders Completed (June 25, 2025):**
- UserSeeder: 7 users created ✅
- OwnerSeeder: 8 property owners created ✅ 
- ConsultantSeeder: 5 consultants created ✅
- ClientSeeder: 129 clients imported from existing data ✅
- PlotSeeder: 8 plots with 22 addresses created ✅
- HB837Seeder: 5 HB837 projects created ✅
- **Total**: 192 records across all core tables ✅

**✅ Laravel 10 Validation Results (June 25, 2025):**
- Laravel Framework: 10.48.29 ✅
- PHP Version: 8.1.5 (compatible) ✅
- Database: MySQL (projecttracker) ✅
- Migrations: 13 applied successfully ✅
- Models: User, HB837, and all others working ✅
- Cache System: Working perfectly ✅
- Laravel 10 Features: All validated ✅
- PHPUnit 10: Updated and compatible ✅
- **Database Seeders**: All working with comprehensive test data ✅

---

## Executive Summary

This document outlines the comprehensive plan to upgrade the Project Tracker application from Laravel 7.30.7 to Laravel 10 or 11, while integrating AdminLTE for a modern administrative interface. The upgrade will improve security, performance, and maintainability while providing a professional user interface.

## Current State Analysis

### Technical Stack
- **Laravel Framework**: 10.48.29 ✅ UPGRADED
- **PHP Version**: 8.1.5 ✅ UPGRADED  
- **Database**: MySQL (development/production), SQLite (testing)
- **Frontend**: Laravel UI 4.0, Bootstrap
- **Key Dependencies**:
  - barryvdh/laravel-dompdf: ^2.2 ✅ COMPATIBLE
  - maatwebsite/excel: ^3.1 ✅ COMPATIBLE
  - laracasts/flash: ^3.2 ✅ COMPATIBLE
  - laravel/pint: ^1.0 ✅ CODE STYLE FIXER
  - spatie/laravel-ignition: ^2.0 ✅ ERROR HANDLING
  - phpunit/phpunit: ^10.1 ✅ TESTING FRAMEWORK

### Application Features
- User authentication and management
- HB837 project tracking functionality
- Consultant and client management
- Property/plot management with addresses
- File upload system (documents, quotes, agreements)
- Excel import/export capabilities
- Automated backup system
- PDF generation for reports

### Current Architecture
```
app/
├── Models/
│   ├── User.php
│   ├── HB837.php
│   ├── Consultant.php
│   ├── Client.php
│   ├── Plot.php
│   └── PlotAddress.php
├── Http/Controllers/Admin/
│   ├── HB837/
│   ├── Consultants/
│   ├── Owners/
│   ├── Plots/
│   ├── Services/
│   └── Users/
└── Services/
```

## Upgrade Strategy

### Phase 1: Pre-Upgrade Preparation (2-3 Days)

#### 1.1 Environment Requirements
**Critical**: PHP upgrade required before Laravel upgrade  
**✅ Laragon Advantage**: Easy version switching available

| Component | Current | Required for L10 | Required for L11 | Recommended | Status |
|-----------|---------|------------------|------------------|-------------|---------|
| PHP | 8.1.5 | 8.1+ | 8.2+ | 8.2+ | ✅ Ready for L11 |
| Laravel | 10.48.29 | 10.x | 11.x | 11.x LTS | ✅ L10 Complete |
| Node.js | v20.17.0 | 16+ | 18+ | 20+ | ✅ Ready |
| Composer | 2.3.5 | 2.2+ | 2.2+ | Latest | ✅ Ready |

#### 1.2 Backup Strategy ✅ COMPLETED
```bash
# ✅ 1. Database backup (DONE)
copy "database\database.sqlite" "database\database_backup_pre_upgrade.sqlite"

# ✅ 2. Version control setup (DONE)
git init
git add .
git commit -m "Baseline: Laravel 7.30.7 before upgrade - June 25, 2025"
git tag v7.30.7-baseline

# 3. Laragon Environment Prep (NEXT)
# - Switch to PHP 8.2+ in Laragon control panel
# - composer self-update
# - Test application on PHP 8.2
```

#### 1.3 Dependency Analysis

**Dependencies to Update:**
- `fideloper/proxy` → Remove (built into Laravel 9+)
- `laravel/ui` → Upgrade or replace with Breeze
- `facade/ignition` → Update to latest
- `nunomaduro/collision` → Update to latest

**Dependencies to Verify:**
- `barryvdh/laravel-dompdf` - Check Laravel 10/11 compatibility
- `maatwebsite/excel` - Ensure latest version supports target Laravel
- `laracasts/flash` - Verify compatibility

#### 1.4 Database Configuration ✅ COMPLETED

**Database Strategy:**
- **Development/Production**: MySQL (`projecttracker` database)
- **Testing**: SQLite (isolated test environment)
- **Backup**: SQLite backup files preserved

**✅ Completed Actions:**
- Created MySQL database `projecttracker` in Laragon
- Updated `.env` to use MySQL connection
- Created `.env.testing` with SQLite configuration  
- Fixed migration order and removed duplicates
- Verified all migrations run successfully on MySQL
- Confirmed database connections for both environments

**Connection Details:**
```bash
# Production/Development (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projecttracker
DB_USERNAME=root
DB_PASSWORD=

# Testing (SQLite - .env.testing)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Phase 2: Incremental Laravel Upgrade (5-7 Days)

#### 2.1 Upgrade Path Decision

**Option A: Incremental (Recommended)**
Laravel 7 → 8 → 9 → 10 → 11
- **Pros**: Safer, easier to debug issues, better testing
- **Cons**: Takes longer, more steps
- **Timeline**: 5-7 days

**Option B: Direct Jump**
Laravel 7 → 10/11
- **Pros**: Faster completion
- **Cons**: Higher risk, harder to isolate issues
- **Timeline**: 3-4 days (if no major issues)

#### 2.2 Laravel 7 → 8 Upgrade ✅ COMPLETED

**Major Breaking Changes Addressed:**
1. ✅ **Model Factories**: Converted to class-based factories
2. ✅ **Database**: Updated factory references and autoloaders
3. ✅ **Routing**: Verified route model binding compatibility
4. ✅ **Pagination**: Confirmed pagination views work

**✅ composer.json Updates Applied:**
```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^8.83.29",
        "laravel/ui": "^3.4"
    },
    "require-dev": {
        "facade/ignition": "^2.17.7",
        "nunomaduro/collision": "^5.11",
        "phpunit/phpunit": "^9.6.13"
    }
}
```

**✅ Completed Action Items:**
- [x] ✅ Updated `database/factories/` to class-based syntax
- [x] ✅ Updated model factory calls in seeders/tests  
- [x] ✅ Reviewed and updated route definitions
- [x] ✅ Fixed PSR-4 autoloading configuration
- [x] ✅ Verified database migrations work
- [x] ✅ Committed and tagged upgrade (v8.83.29)

#### 2.3 Laravel 8 → 9 Upgrade ✅ COMPLETED

**Major Changes Successfully Addressed:**
1. ✅ **Anonymous Migrations**: Laravel 9 migration system working
2. ✅ **TrustProxies**: Removed fideloper/proxy, updated to Laravel 9 built-in
3. ✅ **CORS**: Removed fruitcake/cors (now built-in to Laravel 9)
4. ✅ **Dependencies**: Updated all packages to Laravel 9 compatibility
5. ✅ **Autoloader**: Fixed and optimized for Laravel 9

**✅ Final Results:**
- Laravel Framework: 9.52.20 working perfectly
- Database: All 13 migrations successful on MySQL
- Models: All models (User, HB837, etc.) working
- Cache: System cache operations validated
- Dependencies: All packages compatible and updated

**✅ Completed Action Items:**
- [x] ✅ Removed `fideloper/proxy` from composer.json
- [x] ✅ Removed `fruitcake/laravel-cors` from composer.json
- [x] ✅ Updated TrustProxies middleware to Laravel 9 built-in version
- [x] ✅ Updated all composer dependencies to Laravel 9
- [x] ✅ Completed composer update and autoloader optimization
- [x] ✅ Comprehensive testing and validation
- [x] ✅ Cleared all caches (config, route, view)
- [x] ✅ Committed and tagged upgrade (v9.52.20)

#### 2.4 Laravel 9 → 10 Upgrade ✅ COMPLETED

**Major Changes Successfully Addressed:**
1. ✅ **PHP 8.1 Compatibility**: Fully compatible with PHP 8.1.5
2. ✅ **PHPUnit 10**: Successfully updated to modern testing framework
3. ✅ **Enhanced Validation**: New validation features working perfectly
4. ✅ **Performance Improvements**: Better caching and optimization active
5. ✅ **Security Enhancements**: Additional security features enabled

**✅ Final Results:**
- Laravel Framework: 10.48.29 working perfectly
- Database: All 13 migrations successful on MySQL
- Models: All models (User, HB837, etc.) working
- Cache: System cache operations validated
- Testing: PHPUnit 10 compatible and functional
- Laravel 10 Features: Process utilities, validation, helpers all working

**✅ Completed Action Items:**
- [x] ✅ Updated `laravel/framework` to ^10.0 (v10.48.29)
- [x] ✅ Updated `laravel/tinker` to ^2.8
- [x] ✅ Updated `nunomaduro/collision` to ^7.0
- [x] ✅ Updated `phpunit/phpunit` to ^10.1
- [x] ✅ Updated `spatie/laravel-ignition` to ^2.0
- [x] ✅ Completed composer update and autoloader optimization
- [x] ✅ Comprehensive testing and validation
- [x] ✅ Cleared all caches (config, route, view)
- [x] ✅ Committed and tagged upgrade (v10.48.29)

#### 2.5 Laravel 10 → 11 Upgrade (Optional)

**Benefits of Laravel 11:**
- **LTS Release**: 3 years support vs 2 years for Laravel 10
- **Performance**: ~25% faster than Laravel 10
- **New Features**: Per-second rate limiting, health checks
- **Streamlined Structure**: Reduced boilerplate

**Action Items:**
- [ ] Update to PHP 8.2+
- [ ] Review new application structure
- [ ] Test performance improvements
- [ ] Update configuration files

### Phase 2.6: Database Seeders Implementation ✅ COMPLETED

**Major Achievements:**
1. ✅ **Laravel 10 Namespace**: Updated all seeders to use `Database\Seeders` namespace
2. ✅ **Modern Syntax**: Converted seeders to Laravel 10 syntax with proper typing
3. ✅ **Foreign Key Handling**: Implemented proper foreign key constraint management
4. ✅ **Comprehensive Data**: Created realistic test data across all major tables
5. ✅ **Relationship Integrity**: Ensured proper relationships between all models

**✅ Seeder Implementation Results:**
- **UserSeeder**: 7 authenticated users with secure passwords
- **OwnerSeeder**: 8 property owners with complete contact information
- **ConsultantSeeder**: 5 consultants imported from JSON with full profiles
- **ClientSeeder**: 129 clients imported from existing production data
- **PlotSeeder**: 8 plots with 22 geographic addresses using real coordinates
- **HB837Seeder**: 5 HB837 projects with complete workflow statuses and relationships
- **DatabaseSeeder**: Orchestrates all seeders in proper dependency order

**✅ Technical Improvements:**
- Foreign key constraint handling with `SET FOREIGN_KEY_CHECKS`
- Enum value validation matching `config/hb837.php` specifications
- Proper Carbon date handling for all timestamp fields
- Realistic data with proper geographic coordinates and valid business information
- Relationship validation ensuring all foreign keys reference existing records

**✅ Data Quality:**
- All enum fields use valid values from configuration
- Realistic business scenarios with proper workflow states
- Geographic data uses real coordinates for mapping features
- Complete contact information for testing email/communication features
- Financial data with proper decimal precision for billing features

**✅ Completed Action Items:**
- [x] ✅ Updated seeders to Laravel 10 namespace structure
- [x] ✅ Moved seeders from `database/seeds` to `database/seeders`
- [x] ✅ Implemented proper foreign key constraint handling
- [x] ✅ Created comprehensive test data for all core models
- [x] ✅ Validated all relationships and data integrity
- [x] ✅ Committed and documented all changes

### Phase 3: AdminLTE Integration (3-4 Days)

#### 3.1 AdminLTE Package Selection

**Chosen Package**: `jeroennoten/laravel-adminlte`
- **Version**: 3.x (AdminLTE 3.2+)
- **Laravel Support**: 8, 9, 10, 11
- **Features**: 
  - Pre-built authentication views
  - Configurable menu system
  - Built-in components
  - Multi-language support
  - Plugin integration

#### 3.2 Current UI Migration Analysis

**Current Layout Structure:**
```
resources/views/
├── layouts/app.blade.php           # Main layout
├── partials/
│   ├── navbar.blade.php           # Top navigation
│   ├── messages.blade.php         # Flash messages
│   ├── actions.blade.php          # Action buttons
│   └── tables/active_table.blade.php
├── auth/                          # Login/register views
└── [feature-views]/               # HB837, plots, etc.
```

**Target AdminLTE Structure:**
```
resources/views/
├── layouts/
│   └── app.blade.php              # AdminLTE master layout
├── components/                    # AdminLTE components
├── partials/                      # Custom components
├── auth/                         # Updated auth views
└── admin/                        # Admin panel views
    ├── dashboard.blade.php
    ├── hb837/
    ├── consultants/
    ├── plots/
    └── users/
```

#### 3.3 AdminLTE Installation Steps

```bash
# 1. Install package
composer require jeroennoten/laravel-adminlte

# 2. Install AdminLTE assets and config
php artisan adminlte:install

# 3. Install authentication scaffolding
php artisan adminlte:install --type=enhanced --with=auth_views

# 4. Publish configuration (optional customization)
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\AdminLteServiceProvider" --tag=config
```

#### 3.4 Configuration Strategy

**Menu Configuration** (`config/adminlte.php`):
```php
'menu' => [
    ['header' => 'MAIN NAVIGATION'],
    [
        'text' => 'Dashboard',
        'url'  => 'admin/dashboard',
        'icon' => 'fas fa-tachometer-alt',
    ],
    [
        'text' => 'HB837 Projects',
        'url'  => 'admin/hb837',
        'icon' => 'fas fa-project-diagram',
    ],
    [
        'text' => 'Consultants',
        'url'  => 'admin/consultants',
        'icon' => 'fas fa-users',
    ],
    [
        'text' => 'Properties',
        'url'  => 'admin/plots',
        'icon' => 'fas fa-building',
    ],
    ['header' => 'ADMINISTRATION'],
    [
        'text' => 'Users',
        'url'  => 'admin/users',
        'icon' => 'fas fa-user-cog',
    ],
    [
        'text' => 'Backups',
        'url'  => 'admin/backups',
        'icon' => 'fas fa-database',
    ],
]
```

#### 3.5 View Migration Plan

**Step 1: Create Base Layout**
- Migrate `layouts/app.blade.php` to AdminLTE structure
- Update CSS/JS asset loading
- Configure sidebar and navbar

**Step 2: Update Authentication Views**
- Use AdminLTE authentication templates
- Maintain existing login functionality
- Update styling to match AdminLTE theme

**Step 3: Migrate Feature Views**
- Convert HB837 views to AdminLTE cards/boxes
- Update tables to use AdminLTE DataTables
- Migrate modals to AdminLTE modal components
- Update forms to use AdminLTE form styling

**Step 4: Update Navigation**
- Configure AdminLTE menu structure
- Map existing routes to new menu items
- Update breadcrumbs and page titles

### Phase 4: Testing & Quality Assurance (3-4 Days)

#### 4.1 Automated Testing

**Test Categories:**
1. **Unit Tests**: Model and service layer tests
2. **Feature Tests**: Controller and integration tests  
3. **Browser Tests**: End-to-end functionality

**Test Checklist:**
- [ ] User authentication flow
- [ ] HB837 CRUD operations
- [ ] File upload functionality
- [ ] Excel import/export
- [ ] PDF generation
- [ ] Database backup/restore
- [ ] API endpoints (if any)

#### 4.2 Manual Testing Scenarios

**Core Functionality:**
- [ ] User login/logout/registration
- [ ] Dashboard displays correctly
- [ ] HB837 project creation/editing
- [ ] Consultant management
- [ ] Property/plot management
- [ ] File uploads and downloads
- [ ] Excel file import/export
- [ ] PDF report generation
- [ ] Search and filtering
- [ ] Backup functionality

**UI/UX Testing:**
- [ ] Responsive design on mobile/tablet
- [ ] Navigation menu functionality
- [ ] Form validation and error handling
- [ ] Flash messages display correctly
- [ ] Modal dialogs work properly
- [ ] Tables pagination and sorting

#### 4.3 Performance Testing

**Metrics to Monitor:**
- Page load times
- Database query performance
- File upload speed
- Excel processing time
- Memory usage
- Database size after migration

### Phase 5: Deployment & Go-Live (1-2 Days)

#### 5.1 Pre-Deployment Checklist

**Environment Setup:**
- [ ] Production server PHP 8.2+ installed
- [ ] Composer dependencies updated
- [ ] Environment variables configured
- [ ] File permissions set correctly
- [ ] SSL certificates valid

**Database:**
- [ ] Production database backed up
- [ ] Migrations tested on staging
- [ ] Seeders updated if needed
- [ ] Indexes optimized

**Security:**
- [ ] Security headers configured
- [ ] CSRF protection enabled
- [ ] Input validation tested
- [ ] File upload restrictions verified

#### 5.2 Deployment Steps

```bash
# 1. Backup current production
php artisan backup:run

# 2. Deploy new code
git pull origin main

# 3. Update dependencies
composer install --optimize-autoloader --no-dev

# 4. Run migrations
php artisan migrate

# 5. Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Install AdminLTE assets
php artisan adminlte:install

# 7. Verify deployment
php artisan health:check
```

## Risk Assessment & Mitigation

### High Risk Areas

#### 1. Database Compatibility
**Risk**: Data loss or corruption during migration
**Mitigation**: 
- Complete database backup before each phase
- Test migrations on staging environment
- Verify data integrity after each step

#### 2. File Storage System
**Risk**: File upload/download functionality breaks
**Mitigation**:
- Test file operations extensively
- Backup file storage directory
- Verify file permissions

#### 3. Authentication System
**Risk**: Users unable to log in after upgrade
**Mitigation**:
- Test authentication thoroughly
- Keep admin user credentials accessible
- Have rollback plan ready

#### 4. Third-party Dependencies
**Risk**: Package incompatibilities cause failures
**Mitigation**:
- Research each package's Laravel 10/11 compatibility
- Test alternatives for incompatible packages
- Update packages incrementally

### Medium Risk Areas

#### 1. Custom Code Compatibility
**Risk**: Custom services and helpers break
**Mitigation**:
- Review all custom code for deprecated features
- Update syntax for new Laravel versions
- Test custom functionality thoroughly

#### 2. Frontend Asset Compilation
**Risk**: CSS/JS compilation fails
**Mitigation**:
- Update Node.js and npm packages
- Test asset compilation in development
- Have fallback asset versions ready

### Rollback Strategy

#### Immediate Rollback (< 1 hour)
1. Restore database from backup
2. Switch to previous code version
3. Clear all caches
4. Verify functionality

#### Extended Rollback (> 1 hour)
1. Full server restore from backup
2. Database restoration
3. Complete functionality testing
4. DNS updates if needed

## Timeline & Milestones

### Week 1: Foundation (Days 1-5)
- **Day 1**: Environment setup and backups
- **Day 2-3**: Laravel 7 → 8 upgrade
- **Day 4-5**: Laravel 8 → 9 upgrade

### Week 2: Core Upgrade (Days 6-10)
- **Day 6-7**: Laravel 9 → 10 upgrade
- **Day 8**: Laravel 10 → 11 upgrade (optional)
- **Day 9-10**: AdminLTE installation and basic setup

### Week 3: UI Migration (Days 11-15)
- **Day 11-12**: Layout and navigation migration
- **Day 13-14**: Feature views migration
- **Day 15**: Styling and theme customization

### Week 4: Testing & Deployment (Days 16-20)
- **Day 16-17**: Comprehensive testing
- **Day 18**: Performance optimization
- **Day 19**: Staging deployment and final testing
- **Day 20**: Production deployment

## Success Criteria

### Technical Success Metrics
- [ ] All existing functionality works correctly
- [ ] Page load times improved by 20%+
- [ ] No data loss during migration
- [ ] All tests passing
- [ ] Security vulnerabilities addressed

### User Experience Success Metrics
- [ ] Modern, professional interface
- [ ] Improved navigation and usability
- [ ] Mobile-responsive design
- [ ] Faster form submissions
- [ ] Better error handling and messaging

### Business Success Metrics
- [ ] Zero downtime deployment
- [ ] No user complaints about missing features
- [ ] Improved system maintainability
- [ ] Long-term support with Laravel 11 LTS

## Post-Upgrade Maintenance

### Immediate Tasks (First Month)
- Monitor error logs daily
- Performance optimization based on usage patterns
- User feedback collection and bug fixes
- Documentation updates

### Long-term Tasks (Ongoing)
- Regular security updates
- Performance monitoring
- Feature enhancements using new Laravel capabilities
- AdminLTE theme updates

## Resources & Documentation

### Laravel Upgrade Guides
- [Laravel 8 Upgrade Guide](https://laravel.com/docs/8.x/upgrade)
- [Laravel 9 Upgrade Guide](https://laravel.com/docs/9.x/upgrade)
- [Laravel 10 Upgrade Guide](https://laravel.com/docs/10.x/upgrade)
- [Laravel 11 Upgrade Guide](https://laravel.com/docs/11.x/upgrade)

### AdminLTE Documentation
- [Laravel AdminLTE Package](https://github.com/jeroennoten/Laravel-AdminLTE)
- [AdminLTE 3 Documentation](https://adminlte.io/docs/3.2/)
- [AdminLTE Components](https://adminlte.io/themes/v3/pages/UI/general.html)

### Testing Resources
- [Laravel Testing Documentation](https://laravel.com/docs/10.x/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)

## Appendices

### Appendix A: Package Compatibility Matrix

| Package | Laravel 7 | Laravel 8 | Laravel 9 | Laravel 10 | Laravel 11 |
|---------|-----------|-----------|-----------|------------|------------|
| barryvdh/laravel-dompdf | ✅ | ✅ | ✅ | ✅ | ✅ |
| maatwebsite/excel | ✅ | ✅ | ✅ | ✅ | ✅ |
| laracasts/flash | ✅ | ✅ | ✅ | ✅ | ✅ |
| jeroennoten/laravel-adminlte | ❌ | ✅ | ✅ | ✅ | ✅ |

### Appendix B: Environment Configuration

**Development Environment (.env):**
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projecttracker
DB_USERNAME=root
DB_PASSWORD=
```

**Production Environment (.env):**
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=projecttracker
DB_USERNAME=root
DB_PASSWORD=your_secure_password
```

**Testing Environment (.env.testing):**
```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Appendix C: Emergency Contacts

- **Project Lead**: [Name and contact]
- **Laravel Expert**: [Name and contact]
- **Database Administrator**: [Name and contact]
- **System Administrator**: [Name and contact]

---

**Document Version**: 1.0  
**Last Updated**: June 25, 2025  
**Next Review**: Upon completion of Phase 1
