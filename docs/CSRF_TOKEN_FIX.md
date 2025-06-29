# CSRF Token Fix for 419 Errors

## Problem
Users were experiencing 419 "Page Expired" errors after refreshing the login page or after periods of inactivity.

## Root Cause
1. **Short Session Lifetime**: Session was set to 120 minutes (2 hours)
2. **Missing CSRF Token Refresh**: No mechanism to refresh CSRF tokens when they expire
3. **Incomplete CSRF Setup**: Missing proper CSRF token handling for AJAX requests and form submissions

## Solutions Implemented

### 1. Extended Session Lifetime
- **File**: `.env`
- **Change**: Increased `SESSION_LIFETIME` from 120 to 480 minutes (8 hours)
- **Reason**: Gives users more time before session expires

### 2. Enhanced CSRF Token Handling in JavaScript
- **File**: `resources/js/bootstrap.js`
- **Change**: Added proper CSRF token setup for axios requests
- **Benefit**: All AJAX requests automatically include CSRF token

### 3. jQuery CSRF Setup and Error Handling
- **File**: `resources/views/layouts/app.blade.php`
- **Changes**:
  - Added `$.ajaxSetup()` to include CSRF token in all jQuery AJAX requests
  - Added window focus handler to refresh tokens when user returns to tab
  - Added 419 error handler to prompt user to refresh page

### 4. CSRF Token Refresh Route
- **File**: `routes/web.php`
- **Change**: Added `/admin/csrf-token` route to get fresh tokens
- **Purpose**: Allows JavaScript to refresh tokens without page reload

### 5. Login Form CSRF Refresh
- **File**: `resources/views/auth/login.blade.php`
- **Changes**:
  - Added form submission handler to refresh CSRF token before login
  - Added periodic token refresh (every 30 minutes)
  - Added form ID for JavaScript targeting

## How It Works

1. **Page Load**: Fresh CSRF token is generated and included in meta tag and form
2. **Form Submission**: JavaScript gets fresh token before submitting login form
3. **AJAX Requests**: All AJAX requests automatically include current CSRF token
4. **Token Refresh**: Tokens are refreshed when user focuses window or every 30 minutes
5. **Error Handling**: 419 errors prompt user to refresh page

## Testing
1. Load login page
2. Wait or refresh browser
3. Submit login form - should work without 419 error
4. Check browser console for any CSRF-related errors

## Files Modified
- `.env` - Extended session lifetime
- `resources/js/bootstrap.js` - Axios CSRF setup
- `resources/views/layouts/app.blade.php` - jQuery CSRF setup and error handling
- `routes/web.php` - CSRF token refresh route
- `resources/views/auth/login.blade.php` - Login form CSRF handling

## Next Steps
1. Test login functionality after refresh
2. Monitor for any remaining 419 errors
3. Consider implementing same pattern for other forms if needed
4. Run `npm run development` to rebuild assets with CSRF changes
