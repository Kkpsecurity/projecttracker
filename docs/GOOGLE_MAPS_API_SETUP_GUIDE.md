# Google Maps API Key Setup Guide

## 🗝️ Getting Your Google Maps API Key

### Step 1: Go to Google Cloud Console
1. Visit: https://console.cloud.google.com/
2. Sign in with your Google account
3. Create a new project or select an existing one

### Step 2: Enable Required APIs
1. Go to **APIs & Services** > **Library**
2. Search for and enable these APIs:
   - **Maps JavaScript API** (Required for map display)
   - **Geocoding API** (Required for address-to-coordinates conversion)

### Step 3: Create API Key
1. Go to **APIs & Services** > **Credentials**
2. Click **+ CREATE CREDENTIALS** > **API key**
3. Copy the generated API key

### Step 4: Secure Your API Key (Recommended)
1. Click on your newly created API key to edit it
2. Under **Application restrictions**, choose **HTTP referrers**
3. Add your domain(s):
   - `http://projecttracker_fresh.test/*`
   - `http://localhost/*`
   - `https://yourdomain.com/*` (if deploying to production)
4. Under **API restrictions**, select **Restrict key** and choose:
   - Maps JavaScript API
   - Geocoding API

### Step 5: Add Key to Your Laravel Application
1. Open your `.env` file
2. Replace the placeholder with your actual API key:
   ```
   GOOGLE_MAPS_API_KEY=YOUR_ACTUAL_API_KEY_HERE
   ```
3. Clear Laravel cache:
   ```
   php artisan config:clear
   php artisan cache:clear
   ```

## 🧪 Testing Your Setup

### Method 1: Basic Test
1. Navigate to `/admin/maps` in your browser
2. If the map loads without errors, your API key is working

### Method 2: Console Check
1. Open browser Developer Tools (F12)
2. Check Console for any Google Maps errors
3. A working setup should show no API-related errors

### Method 3: Feature Test
1. Try entering an address in the "Add Plot from Address" field
2. If geocoding works, both APIs are properly configured

## 💰 Pricing Information

### Free Tier
- **$200 monthly credit** (enough for most development/small apps)
- **28,500+ map loads per month** for free
- **40,000+ geocoding requests per month** for free

### Beyond Free Tier
- Maps JavaScript API: $7 per 1,000 loads
- Geocoding API: $5 per 1,000 requests

## 🚨 Common Issues & Solutions

### Issue: "This page can't load Google Maps correctly"
**Solution**: API key is missing or invalid
- Verify key is correctly set in `.env`
- Check that key hasn't been restricted too heavily

### Issue: Map loads but geocoding doesn't work
**Solution**: Geocoding API not enabled
- Enable Geocoding API in Google Cloud Console
- Wait a few minutes for activation

### Issue: API key error in console
**Solution**: Domain restrictions too strict
- Add your current domain to HTTP referrers
- For development, allow `localhost` and your local domain

### Issue: Map works locally but not in production
**Solution**: Update domain restrictions
- Add production domain to API key restrictions
- Update `.env` file on production server

## 🔒 Security Best Practices

1. **Never commit API keys to version control**
   - Use `.env` files (already in `.gitignore`)
   - Use environment variables on production

2. **Always restrict your API keys**
   - Set HTTP referrer restrictions
   - Limit to only required APIs

3. **Monitor usage**
   - Set up billing alerts in Google Cloud
   - Monitor API usage in console

4. **Rotate keys periodically**
   - Generate new keys every few months
   - Disable old keys after updating

## 📞 Support

If you need help with API key setup:
1. Check Google Maps Platform documentation
2. Contact your development team
3. Review Google Cloud Console billing and usage

---

**Next Step**: Get your API key and update the `.env` file, then test the maps functionality!
