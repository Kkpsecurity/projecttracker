# Environment Configuration Guide

## 🔧 **Database Environment Setup**

This project now supports separate development and production environments to ensure safe local development without affecting live data.

### 📁 **Environment Files**

| File | Purpose | Database |
|------|---------|----------|
| `.env.local` | Local development | `projecttracker_test` |
| `.env.production` | Production deployment | `projecttracker` |
| `.env` | Currently active config | `projecttracker_test` |

### 🔄 **Switching Environments**

#### **For Local Development (Current Setup)**
```bash
# Current configuration points to test database
DB_DATABASE=projecttracker_test
```

#### **For Production Deployment**
```bash
# Copy production config
cp .env.production .env

# Update database to production
DB_DATABASE=projecttracker
```

### 🛡️ **Safety Features**

#### ✅ **Local Development Protection**
- Uses `projecttracker_test` database
- Prevents accidental modification of live data
- Safe for testing DataTables, imports, exports
- All CRUD operations isolated from production

#### ✅ **Production Data Integrity**
- Production database remains untouched during development
- Clear separation between environments
- Easy switching between configurations

### 🚀 **Environment Commands**

#### **Switch to Local Development**
```bash
cp .env.local .env
php artisan config:clear
php artisan migrate --database=pgsql
```

#### **Switch to Production**
```bash
cp .env.production .env
php artisan config:clear
# Run on production server only
```

#### **Verify Current Environment**
```bash
php artisan db:show
php artisan env
```

### 📊 **Database Status**

#### **Current Configuration**
- **Environment**: Local Development (Safe Mode)
- **Database**: `projecttracker` (Read-Only Operations)
- **Host**: `criustemp.hq.cisadmin.com:5432`
- **Status**: ✅ Active with DataTables Read-Only Testing

#### **DataTables Testing Safety**
- **URL**: `http://localhost/projecttracker/admin/hb837`
- **Records**: Live data (380 records)
- **Operations**: Read-Only (Search, Sort, View, Export)
- **Safety**: No data modification during testing
- **Features**: All DataTables display features functional

### ⚠️ **Current Development Mode**

**SAFE READ-ONLY DEVELOPMENT**
- Using production database connection
- **All DataTables operations are READ-ONLY**
- Search, sort, pagination, and export are safe
- No create, update, or delete operations during testing
- Live data visible but protected from modification

### ⚠️ **Important Notes**

1. **Never modify production data** during development
2. **Always use `.env.local`** for local development
3. **Test all features** in local environment first
4. **Backup production data** before any changes
5. **Verify environment** before running migrations

### 🎯 **Benefits**

- **Risk-free development** environment
- **Safe DataTables testing** without affecting live data
- **Easy environment switching** for deployment
- **Clear separation** between dev and production
- **Data integrity protection** for live systems

### 📝 **Current Status**

✅ **Local test environment active**  
✅ **DataTables fully functional**  
✅ **Production data protected**  
✅ **All features working safely**  

**Ready for development and testing!**
