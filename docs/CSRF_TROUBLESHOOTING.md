# CSRF 419 Error Troubleshooting Guide

## Current Status
- ✅ CSRF token endpoint working: `/admin/csrf-token` returns valid tokens
- ✅ JavaScript CSRF handling implemented in login form
- ✅ Session lifetime extended to 8 hours
- ✅ No 419 errors appearing in Laravel logs
- ❌ Still getting 419 "Page Expired" error on login

## Debugging Steps

### 1. Browser Console Check
Open browser dev tools (F12) and check console for:
- "Login page JavaScript loaded" 
- "Current CSRF token: [token]"
- "Form CSRF token: [token]"
- "Form submission intercepted"
- "Fresh CSRF token received: [token]"

### 2. Network Tab Check
In dev tools Network tab, look for:
- GET request to `/admin/csrf-token` - should return 200 with JSON token
- POST request to `/admin/login` - check request headers for X-CSRF-TOKEN
- Response status - should not be 419

### 3. Common Issues & Solutions

#### Issue: JavaScript not loading
**Solution:** Clear browser cache (Ctrl+F5) or hard refresh

#### Issue: Form bypassing JavaScript
**Check:** Form has `id="loginForm"` attribute
**Fix:** Ensure form has correct ID

#### Issue: Token mismatch despite refresh
**Check:** Session cookies are being set correctly
**Fix:** Check Application > Cookies in dev tools

#### Issue: AJAX request failing
**Check:** Network tab for CORS or other errors
**Fix:** Ensure proper domain configuration

### 4. Manual Testing Commands

```bash
# Test CSRF endpoint
Invoke-RestMethod -Uri "http://projecttracker.test/admin/csrf-token"

# Check Laravel logs
Get-Content storage\logs\laravel.log -Tail 20

# Clear all caches
php artisan optimize:clear
```

### 5. Emergency Bypass (Temporary)

If debugging takes too long, can temporarily disable CSRF for login:

```php
// In app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'admin/login',  // TEMPORARY - remove after fixing
];
```

**⚠️ WARNING: Remove this bypass after fixing the real issue!**

### 6. Session Issues Check

Common session problems:
- Cookie domain mismatch
- Secure cookie on HTTP
- Browser blocking cookies
- Session storage permissions

### 7. Browser-Specific Issues

- **Chrome**: Check for SameSite cookie warnings
- **Firefox**: Check for tracking protection blocking
- **Safari**: Check for cookie blocking settings

## Next Steps

1. Open browser dev tools and test login
2. Check console for JavaScript debug messages
3. Check network tab for AJAX requests
4. Report what you see in console/network
5. If needed, we can implement emergency bypass

## Files Modified
- `resources/views/auth/login.blade.php` - Added debug logging
- `resources/views/layouts/app.blade.php` - CSRF handling
- `resources/js/bootstrap.js` - Axios CSRF setup
- `routes/web.php` - CSRF token endpoint
