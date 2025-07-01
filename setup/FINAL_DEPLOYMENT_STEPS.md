# FINAL DEPLOYMENT STEPS - Add Role Column & Complete Setup

## 🎯 **OBJECTIVE**
Complete the deployment by adding the missing `role` column to the users table and creating admin accounts.

## 📋 **WHAT WE KNOW**
- ✅ Database connection is working
- ✅ Most migrations completed successfully  
- ✅ Users table exists
- ❌ `role` column is missing from users table
- ❌ User seeder failed due to missing `role` column

## 🚀 **AUTOMATED SOLUTION (RECOMMENDED)**

### Option 1: Run the Complete Deployment Script
```bash
# On your server, run:
php complete_server_deployment.php
```

### Option 2: Use the Shell Script
```bash
# Make executable and run:
chmod +x complete_deployment.sh
bash complete_deployment.sh
```

## 🔧 **MANUAL SOLUTION (If Automated Fails)**

### Step 1: Add Role Column to Users Table
```bash
# Connect via SSH to your server
ssh forge@projecttracker.hb837training.com

# Navigate to project directory
cd /home/forge/projecttracker.hb837training.com

# Add role column using Laravel Tinker
php artisan tinker
```

In Tinker, run:
```php
>>> Illuminate\Support\Facades\DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'editor'");
>>> exit
```

### Step 2: Verify Role Column Added
```bash
php artisan tinker
```

In Tinker:
```php
>>> Illuminate\Support\Facades\Schema::hasColumn('users', 'role');
// Should return: true
>>> exit
```

### Step 3: Run User Seeder
```bash
php artisan db:seed --class=UserSeeder --force
```

### Step 4: Verify Users Created
```bash
php artisan tinker
```

In Tinker:
```php
>>> App\Models\User::count();
>>> App\Models\User::all(['name', 'email', 'role', 'is_admin']);
>>> exit
```

### Step 5: Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## ✅ **VERIFICATION CHECKLIST**

After completing the steps above, verify:

- [ ] Role column exists in users table
- [ ] 7 admin users created with different roles
- [ ] All users have `is_admin = true`
- [ ] All users use password `Secure$101`
- [ ] Website loads at https://projecttracker.hb837training.com
- [ ] Login works with admin credentials
- [ ] Admin dashboard accessible

## 👤 **ADMIN LOGIN CREDENTIALS**

### Primary Admin Account
```
Email: richievc@gmail.com
Password: Secure$101
Role: superadmin
```

### Other Admin Accounts
```
gundrycs@cisadmin.com - manager
jonesy@cisworldservices.org - superadmin  
poulinkc@cisadmin.com - editor
ashley@s2institute.com - manager
rodrighb@cisworldservices.org - manager
sgundry@s2institute.com - auditor
```

All passwords: `Secure$101`

## 🚨 **TROUBLESHOOTING**

### If Role Column Addition Fails
```sql
-- Direct SQL approach
ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'editor';
```

### If User Seeder Fails
Check if the User model has role in fillable:
```bash
php artisan tinker
>>> $user = new App\Models\User();
>>> $user->getFillable();
// Should include 'role'
```

### If Database Connection Fails
Check .env file:
```bash
cat .env | grep DB_
```

## 🎉 **SUCCESS INDICATORS**

When deployment is complete, you should see:
- Website loads without errors
- Login page accessible
- Admin login works
- Dashboard shows admin features
- No database errors in logs

## 📞 **NEED HELP?**

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check server error logs
3. Verify .env database credentials
4. Test database connection: `php artisan migrate:status`
