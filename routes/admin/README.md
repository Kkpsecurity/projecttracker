# Admin Routes Organization

This directory contains organized route files for different sections of the admin panel.

## File Structure

```
routes/admin/
├── analytics.php       # Analytics and reporting routes
├── consultants.php     # Consultant management routes
├── hb837.php          # HB837 property inspection routes
├── import-config.php   # HB837 import configuration routes
├── logs.php           # System activity logs routes
├── maps.php           # Google Maps integration routes
├── plots.php          # Property plots management routes
├── settings.php       # System settings routes
├── system.php         # System maintenance and API routes
└── users.php          # User management routes
```

## Route Organization Guidelines

### 1. Route Order
- Non-parameterized routes **BEFORE** parameterized routes
- Specific routes **BEFORE** generic routes
- API endpoints grouped in separate prefixes

### 2. Naming Conventions
- File names match the primary route prefix
- Use descriptive comments for route sections
- Group related functionality together

### 3. Controller Imports
- Controllers are imported in individual route files
- Each file is self-contained with its own use statements

### 4. Route Groups
- All routes use proper prefix and name grouping
- Consistent naming patterns across files
- Clear separation between different modules

## Usage

Routes are automatically loaded via `require_once` statements in the main `admin.php` file:

```php
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Module-specific routes
    require_once __DIR__ . '/admin/analytics.php';
    require_once __DIR__ . '/admin/users.php';
    require_once __DIR__ . '/admin/settings.php';
    // ... etc
});
```

## Benefits

1. **Better Organization**: Each module has its own route file
2. **Easier Maintenance**: Changes are isolated to specific files
3. **Improved Readability**: Smaller, focused route files
4. **Team Collaboration**: Multiple developers can work on different modules
5. **Performance**: Only relevant routes are loaded per file

## Adding New Routes

1. Create a new file in `routes/admin/` directory
2. Follow the naming convention (prefix + `.php`)
3. Include proper documentation headers
4. Add the require_once statement to main `admin.php`
5. Follow existing patterns for consistency
