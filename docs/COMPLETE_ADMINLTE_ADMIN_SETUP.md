# 🎛️ Complete AdminLTE Admin Dashboard Setup

**Goal**: Full AdminLTE admin dashboard with user management, roles, and complete admin interface.

---

## 🚀 **Phase 1: Foundation Setup**

### **Create Fresh Laravel Project**
```powershell
cd c:\laragon\www
composer create-project laravel/laravel projecttracker_fresh
cd projecttracker_fresh
php artisan key:generate
```

### **Install Core Dependencies**
```powershell
# Essential packages
composer require laravel/ui
composer require jeroennoten/laravel-adminlte
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require laracasts/flash
composer require maatwebsite/excel
composer require intervention/image
```

---

## 🎨 **Phase 2: AdminLTE Full Installation**

### **Install AdminLTE with All Features**
```powershell
# Generate basic auth
php artisan ui bootstrap --auth

# Install AdminLTE with enhanced features
php artisan adminlte:install --type=enhanced --with=auth_views,main_views

# Publish all AdminLTE resources
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\AdminLteServiceProvider"

# Install AdminLTE plugins
php artisan adminlte:plugins install --plugin=datatables
php artisan adminlte:plugins install --plugin=select2
php artisan adminlte:plugins install --plugin=chartjs
php artisan adminlte:plugins install --plugin=sweetalert2
```

---

## 👥 **Phase 3: User Management & Permissions Setup**

### **Database Configuration**
Edit `.env`:
```env
APP_NAME="ProjectTracker Admin"
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

### **Setup Permissions & Roles**
```powershell
# Publish permission migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Run all migrations
php artisan migrate:fresh

# Create admin seeder
php artisan make:seeder AdminSeeder
php artisan make:seeder RolePermissionSeeder
```

---

## 🔧 **Phase 4: Create Admin Controllers & Views**

### **Generate Admin Controllers**
```powershell
# User management
php artisan make:controller Admin/UserController --resource
php artisan make:controller Admin/RoleController --resource
php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/ProfileController

# Settings
php artisan make:controller Admin/SettingsController
```

### **Generate Admin Models**
```powershell
php artisan make:model Role
php artisan make:model Permission
```

---

## 📊 **Phase 5: Dashboard Features**

### **AdminLTE Configuration** (`config/adminlte.php`)
```php
return [
    'title' => 'ProjectTracker Admin',
    'title_prefix' => '',
    'title_postfix' => ' | Admin Panel',
    
    'logo' => '<b>Project</b>Tracker',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    
    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    
    'menu' => [
        // Dashboard
        [
            'text' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'fas fa-tachometer-alt',
        ],
        
        // User Management
        ['header' => 'USER MANAGEMENT'],
        [
            'text' => 'Users',
            'route' => 'admin.users.index',
            'icon' => 'fas fa-users',
            'can' => 'manage users',
        ],
        [
            'text' => 'Roles & Permissions',
            'route' => 'admin.roles.index',
            'icon' => 'fas fa-user-shield',
            'can' => 'manage roles',
        ],
        
        // Business Logic (placeholder for migration)
        ['header' => 'PROJECT MANAGEMENT'],
        [
            'text' => 'HB837 Management',
            'route' => 'admin.hb837.index',
            'icon' => 'fas fa-project-diagram',
        ],
        [
            'text' => 'Consultants',
            'route' => 'admin.consultants.index',
            'icon' => 'fas fa-user-tie',
        ],
        
        // System
        ['header' => 'SYSTEM'],
        [
            'text' => 'Settings',
            'route' => 'admin.settings.index',
            'icon' => 'fas fa-cogs',
            'can' => 'manage settings',
        ],
        [
            'text' => 'Backup',
            'route' => 'admin.backup.index',
            'icon' => 'fas fa-database',
            'can' => 'manage backups',
        ],
    ],
];
```

---

## 🛣️ **Phase 6: Admin Routes**

### **Routes Setup** (`routes/web.php`)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;

// Public routes
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Auth routes (AdminLTE styled)
Auth::routes();

// Admin routes (protected)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    
    // User management
    Route::resource('users', UserController::class);
    Route::put('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Roles & Permissions
    Route::resource('roles', RoleController::class);
    
    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Placeholder routes for future migration
    Route::get('hb837', function() { return view('admin.hb837.index'); })->name('hb837.index');
    Route::get('consultants', function() { return view('admin.consultants.index'); })->name('consultants.index');
    Route::get('backup', function() { return view('admin.backup.index'); })->name('backup.index');
});
```

---

## 📝 **Phase 7: Create Seeders**

### **AdminSeeder** (`database/seeders/AdminSeeder.php`)
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'manage users',
            'manage roles',
            'manage settings',
            'manage backups',
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Assign permissions to admin
        $adminRole->givePermissionTo($permissions);
        $userRole->givePermissionTo(['view dashboard']);

        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@projecttracker.test',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('admin');

        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@projecttracker.test',
            'password' => bcrypt('user123'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('user');
    }
}
```

---

## 🏃‍♂️ **Phase 8: Final Setup Commands**

### **Run Complete Setup**
```powershell
# Create fresh database (do this in your PostgreSQL client first)
# CREATE DATABASE projecttracker_fresh;

# Run migrations and seeders
php artisan migrate:fresh
php artisan db:seed --class=AdminSeeder

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Start the server
php artisan serve --host=127.0.0.1 --port=8000
```

---

## ✅ **Testing the Complete Setup**

### **URLs to Test:**
- `http://127.0.0.1:8000/login` - AdminLTE login
- `http://127.0.0.1:8000/admin/dashboard` - Main dashboard
- `http://127.0.0.1:8000/admin/users` - User management
- `http://127.0.0.1:8000/admin/roles` - Role management

### **Test Accounts:**
- **Admin**: `admin@projecttracker.test` / `admin123`
- **User**: `user@projecttracker.test` / `user123`

### **Expected Features:**
- ✅ Beautiful AdminLTE dashboard
- ✅ User management with roles/permissions
- ✅ Profile management
- ✅ Settings page
- ✅ **Working CSRF (no 419 errors)**
- ✅ Responsive design
- ✅ DataTables, Select2, Charts ready

---

## 🎯 **Success Criteria**

- [ ] AdminLTE dashboard loads perfectly
- [ ] User management fully functional
- [ ] Role-based access control working
- [ ] No CSRF token errors
- [ ] All AdminLTE features working
- [ ] Ready for business logic migration

**This creates a production-ready admin system foundation!**
