# HB837 Custom Fields System - Updated Implementation

## Overview

The HB837 import system has been updated to distinguish between **Config Fields** (immutable from configuration) and **Custom Fields** (fully manageable by users).

## Key Changes Made

### 1. Database Structure
- Added `is_config_field` and `is_custom_field` columns to `hb837_import_field_configs` table
- Migration: `2025_07_01_213156_add_field_type_columns_to_hb837_import_field_configs_table.php`

### 2. Model Updates
- **HB837ImportFieldConfig Model**: Added new scopes and methods:
  - `scopeConfigFields()` - Get config fields (immutable)
  - `scopeCustomFields()` - Get custom fields (manageable)
  - `scopeDeletable()` - Get fields that can be deleted
  - `canModifyStructure()` - Check if field structure can be modified
  - `isConfigField()` / `isCustomField()` - Field type checking methods

### 3. Import Logic Updates
- **EnhancedHB837Import**: Updated to load field mappings from database instead of config file
- Fallback to config file if database is empty
- Better error handling and logging

### 4. Admin Interface Updates
- **Index View**: Split into two sections:
  - **Config Fields**: Read-only structure, labels can be updated
  - **Custom Fields**: Fully manageable (create, edit, delete)
- **Create/Edit**: New custom fields are automatically marked as `is_custom_field = true`
- **Delete Protection**: Config fields and system fields cannot be deleted

### 5. HB837 Edit Form Enhancement
- Added **Custom Fields** tab to HB837 edit form
- Dynamic field generation based on custom field configurations
- Support for different field types: text, textarea, number, date, enum, etc.
- Form validation integration

### 6. Menu Integration
- Added **HB837 Import Config** menu item to the Admin Center submenu
- Located at: Admin Center → HB837 Import Config
- Route: `admin.hb837-import-config.index`
- Icon: Font Awesome cog icon

## Field Type Hierarchy

### System Fields (SYS)
- Cannot be modified or deleted
- Examples: `id`, `user_id`, `created_at`, `updated_at`

### Config Fields (CFG) 
- Structure is immutable (defined in config)
- Labels and descriptions can be updated
- Cannot be deleted
- Examples: `property_name`, `address`, `quoted_price`

### Custom Fields (CUSTOM)
- Fully manageable by users
- Can be created, edited, and deleted
- Support for various field types
- Automatically get database columns created

## Usage Guide

### For Administrators

1. **View Field Configuration**: Navigate to Admin → HB837 Import Configuration
2. **Config Fields Section**: View and update labels for default fields
3. **Custom Fields Section**: Manage user-defined fields
4. **Create Custom Field**: Click "Add New Field" to create custom fields
5. **Edit HB837 Records**: Use the "Custom Fields" tab to manage custom field values

### For Developers

#### Adding New Config Fields
Update the `config/hb837_field_mapping.php` file and re-run the seeder:
```bash
php artisan db:seed --class=HB837ImportFieldConfigSeeder
```

#### Creating Custom Fields Programmatically
```php
HB837ImportFieldConfig::create([
    'database_field' => 'custom_priority_level',
    'field_label' => 'Priority Level',
    'description' => 'Project priority level',
    'field_type' => 'enum',
    'enum_values' => ['low', 'medium', 'high', 'urgent'],
    'is_config_field' => false,
    'is_custom_field' => true,
    'is_active' => true,
]);
```

#### Accessing Field Values in Code
```php
// Get all custom fields
$customFields = HB837ImportFieldConfig::customFields()->active()->get();

// Get config fields
$configFields = HB837ImportFieldConfig::configFields()->active()->get();

// Access custom field value
$customValue = $hb837->custom_priority_level;
```

## File Locations

### Models
- `app/Models/HB837ImportFieldConfig.php` - Field configuration model

### Controllers
- `app/Http/Controllers/Admin/HB837ImportConfigController.php` - Field management

### Views
- `resources/views/admin/hb837-import-config/index.blade.php` - Split view for config/custom fields
- `resources/views/admin/hb837/edit.blade.php` - Added custom fields tab

### Migrations
- `database/migrations/2025_07_01_150000_create_hb837_import_field_configs_table.php` - Original table
- `database/migrations/2025_07_01_213156_add_field_type_columns_to_hb837_import_field_configs_table.php` - Field type columns

### Import Logic
- `app/Imports/EnhancedHB837Import.php` - Updated to use database configuration

## Benefits

1. **Clear Separation**: Config fields vs custom fields are clearly distinguished
2. **Protection**: System and config fields are protected from accidental deletion
3. **Flexibility**: Users can create custom fields as needed
4. **Integration**: Custom fields automatically appear in edit forms
5. **Database-Driven**: All configuration is stored in database for easier management
6. **Fallback**: Config file fallback ensures system always works

## Next Steps

1. **Testing**: Test custom field creation and usage
2. **Validation**: Add more robust validation for custom field types
3. **Import Integration**: Ensure custom fields work in import process
4. **Documentation**: Create user documentation for field management
5. **Permissions**: Add role-based permissions for field management
