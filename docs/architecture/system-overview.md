# System Architecture Overview

**Application**: Project Tracker - Security Consulting Management System  
**Framework**: Laravel 11.45.1  
**Database**: MySQL 8.0+  
**Frontend**: AdminLTE 3.x with Bootstrap 4  
**Last Updated**: June 28, 2025

## 🏗️ High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    PROJECT TRACKER SYSTEM                       │
│                 Security Consulting Management                   │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Admin Panel   │    │   Client Portal │    │   API Endpoints │
│   (AdminLTE)    │    │   (Planned)     │    │   (RESTful)     │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ ✅ User Mgmt    │    │ ⏳ Project View │    │ ⏳ Public API   │
│ ✅ ProTrack     │    │ ⏳ File Access  │    │ ⏳ Mobile App   │
│ ✅ HB837        │    │ ⏳ Status Update│    │ ⏳ Integrations │
│ ✅ Settings     │    │ ⏳ Communication│    │ ⏳ Webhooks     │
│ ✅ Reports      │    │ ⏳ Billing      │    │ ⏳ Third-party  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 🔐 Authentication & Authorization

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Public    │───▶│   Login     │───▶│   Admin     │
│   Routes    │    │   Auth      │    │   Panel     │
└─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Landing     │    │ Laravel     │    │ AdminLTE    │
│ Pages       │    │ Breeze/UI   │    │ Dashboard   │
└─────────────┘    └─────────────┘    └─────────────┘
```

### Authentication Flow
1. **Public Access** - Landing pages, login forms
2. **Laravel Auth** - Built-in authentication system
3. **Admin Middleware** - `IsAdmin` middleware for admin access
4. **Role-based Access** - User roles and permissions
5. **Session Management** - Secure session handling

## 📊 Database Architecture

### Core Tables Structure
```sql
-- User Management
users (id, name, email, password, is_admin, is_active, last_login)
password_resets (email, token, created_at)
site_settings (id, key, value, type, group)

-- Project Management (ProTrack)
clients (id, corporate_name, client_name, project_name, quick_status)
projects (id, client_id, name, status, start_date, end_date)
project_contacts (id, project_id, name, email, phone, role)

-- HB837 Compliance System
hb837_properties (id, property_address, management_company, status)
hb837_inspections (id, property_id, inspector_id, scheduled_date)
hb837_reports (id, property_id, report_date, compliance_status)

-- File Management
project_files (id, project_id, filename, file_path, uploaded_by)
backup_files (id, filename, file_path, created_at, file_size)

-- System Logs
activity_logs (id, user_id, action, model_type, model_id, created_at)
```

### Relationships
```
Users ──┐
        ├─── Projects ──── Clients
        │         │
        │         └──── Project Files
        │
        └─── HB837 Properties ──── Inspections
                     │
                     └──── Reports
```

## 🎨 Frontend Architecture

### AdminLTE Integration
```
resources/views/
├── layouts/
│   ├── admin.blade.php              # Main AdminLTE layout
│   ├── app.blade.php               # Public site layout
│   └── partials/
│       ├── sidebar.blade.php       # Admin navigation
│       ├── navbar.blade.php        # Top navigation
│       └── footer.blade.php        # Footer content
├── admin/
│   ├── dashboard.blade.php         # Admin dashboard
│   ├── users/                      # User management
│   ├── settings/                   # Site settings
│   ├── protrack/                   # Project management
│   └── hb837/                      # HB837 compliance
└── auth/
    ├── login.blade.php             # Login form
    └── passwords/                  # Password reset
```

### Asset Management
```
public/
├── css/
│   ├── admin-lte.min.css          # AdminLTE styles
│   ├── app.css                    # Custom styles
│   └── datatables.css             # DataTables styling
├── js/
│   ├── admin-lte.min.js           # AdminLTE scripts
│   ├── app.js                     # Custom JavaScript
│   └── datatables.js             # DataTables functionality
└── vendor/
    ├── fontawesome/               # Font Awesome icons
    ├── jquery/                    # jQuery library
    └── bootstrap/                 # Bootstrap framework
```

## 🔧 Application Components

### Core Modules

#### 1. User Management System
```php
// Controllers
UserController::class
├── index()         # List users with DataTables
├── create()        # User creation form
├── store()         # Save new user
├── edit()          # User editing form
├── update()        # Update existing user
├── destroy()       # Delete user
├── datatable()     # DataTables AJAX endpoint
└── toggleStatus()  # Activate/deactivate user

// Models
User::class
├── Relationships: hasMany(Projects), hasMany(ActivityLogs)
├── Scopes: active(), admin(), search()
├── Mutators: setPasswordAttribute()
└── Accessors: getStatusBadgeAttribute()
```

#### 2. ProTrack Project Management
```php
// Controllers
HomeController::class (ProTrack)
├── index()         # Project dashboard with tabs
├── datatable()     # DataTables for project listing
├── show()          # Project details view
├── edit()          # Project editing
└── export()        # Export functionality

// Models
Client::class
├── Relationships: hasMany(Projects), hasMany(Contacts)
├── Scopes: byStatus(), search(), recent()
└── Accessors: getStatusColorAttribute()
```

#### 3. HB837 Compliance System
```php
// Controllers
HB837Controller::class
├── index()         # HB837 dashboard
├── properties()    # Property management
├── inspections()   # Inspection scheduling
├── reports()       # Compliance reporting
└── backup()        # System backup

// Models
HB837Property::class
├── Relationships: hasMany(Inspections), hasMany(Reports)
├── Scopes: byComplianceStatus(), byRegion()
└── Methods: calculateComplianceDate()
```

#### 4. Site Settings Management
```php
// Controllers
SettingsController::class
├── index()         # Settings dashboard
├── update()        # Update settings
├── uploadLogo()    # File upload handling
└── resetToDefault() # Reset settings

// Models
SiteSettings::class
├── Singleton pattern implementation
├── Methods: get(), set(), reset()
└── Caching: Redis/file cache integration
```

## 🌐 Route Architecture

### Web Routes Structure
```php
// Public routes
Route::get('/', 'HomeController@welcome');
Route::auth(); // Laravel authentication routes

// Admin routes (protected by IsAdmin middleware)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    
    // User Management
    Route::resource('users', 'UserController');
    Route::post('users/datatable', 'UserController@datatable')->name('users.datatable');
    
    // ProTrack System
    Route::prefix('home')->name('home.')->group(function () {
        Route::get('/', 'HomeController@index')->name('index');
        Route::get('/tabs/{tab}', 'HomeController@index')->name('tabs');
        Route::post('/datatable', 'HomeController@datatable')->name('datatable');
    });
    
    // HB837 System
    Route::prefix('hb837')->name('hb837.')->group(function () {
        Route::get('/', 'HB837Controller@index')->name('index');
        Route::get('/properties', 'HB837Controller@properties')->name('properties');
        Route::get('/backup', 'HB837Controller@backup')->name('backup');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', 'SettingsController@index')->name('index');
        Route::post('/update', 'SettingsController@update')->name('update');
    });
});
```

### API Routes (Planned)
```php
// RESTful API endpoints
Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', 'API\ProjectController');
    Route::apiResource('clients', 'API\ClientController');
    Route::apiResource('properties', 'API\HB837PropertyController');
});
```

## 🔌 External Integrations

### Current Integrations
- **Google Maps API** - Property location mapping
- **Email Services** - SMTP for notifications
- **File Storage** - Local file system (Laravel Storage)

### Planned Integrations
- **Cloud Storage** - AWS S3 or similar
- **Email Marketing** - Mailgun/SendGrid integration
- **Payment Processing** - Stripe for billing
- **Backup Services** - Automated cloud backups
- **SMS Notifications** - Twilio integration

## 📱 Technology Stack

### Backend Technologies
- **Framework**: Laravel 11.45.1
- **PHP Version**: 8.3+
- **Database**: MySQL 8.0+
- **Cache**: File/Redis (configurable)
- **Queue**: Database/Redis (configurable)
- **Session**: File/Database/Redis

### Frontend Technologies
- **UI Framework**: AdminLTE 3.x
- **CSS Framework**: Bootstrap 4
- **JavaScript**: jQuery 3.6+
- **Icons**: Font Awesome 5
- **DataTables**: Yajra DataTables package
- **Charts**: Chart.js (planned)

### Development Tools
- **Composer**: PHP dependency management
- **NPM**: Frontend package management
- **Laravel Mix**: Asset compilation
- **Git**: Version control
- **PhpStorm/VSCode**: Development environment

## 🚀 Performance Architecture

### Caching Strategy
```php
// Model caching
Cache::remember('site_settings', 3600, function () {
    return SiteSettings::all()->pluck('value', 'key');
});

// Query caching
Cache::remember('user_count', 600, function () {
    return User::count();
});

// View caching (for static content)
Route::get('/', function () {
    return Cache::remember('homepage', 3600, function () {
        return view('welcome');
    });
});
```

### Database Optimization
- **Indexing**: Proper indexes on frequently queried columns
- **Query Optimization**: Efficient queries with proper joins
- **Eager Loading**: Prevent N+1 query problems
- **Connection Pooling**: MySQL connection optimization

### Frontend Optimization
- **Asset Minification**: CSS/JS compression
- **CDN Usage**: External library delivery
- **Image Optimization**: Optimized image formats
- **Lazy Loading**: Deferred content loading

## 🔒 Security Architecture

### Security Measures
- **CSRF Protection**: All forms protected with CSRF tokens
- **SQL Injection Prevention**: Eloquent ORM with prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **Authentication**: Laravel's built-in authentication
- **Authorization**: Role-based access control
- **HTTPS Enforcement**: SSL/TLS encryption
- **Input Validation**: Comprehensive form validation

### Data Protection
- **Password Hashing**: Bcrypt password encryption
- **Sensitive Data**: Encryption for sensitive information
- **File Upload Security**: Secure file handling
- **Database Security**: Proper database user permissions
- **Backup Encryption**: Encrypted backup files

## 📊 Monitoring & Logging

### Application Logging
```php
// Activity logging
Log::info('User login', [
    'user_id' => $user->id,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent()
]);

// Error logging
Log::error('Database connection failed', [
    'exception' => $exception->getMessage(),
    'trace' => $exception->getTraceAsString()
]);
```

### Performance Monitoring
- **Query Logging**: Slow query identification
- **Error Tracking**: Exception monitoring
- **User Activity**: Comprehensive activity logs
- **System Metrics**: Server resource monitoring

---

**System Architecture: Robust and Scalable** 🏗️  
**Design Philosophy**: Clean, maintainable, and extensible architecture  
**Future-Ready**: Built for scalability and feature expansion
