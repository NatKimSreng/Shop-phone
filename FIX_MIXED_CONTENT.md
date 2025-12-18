# Fix Mixed Content Error (HTTP/HTTPS)

## The Problem

You're seeing this error:
```
Mixed Content: The page at 'https://...' was loaded over HTTPS, but requested an insecure resource 'http://...'
```

This happens when:
- Your page is served over HTTPS
- But JavaScript/AJAX requests use HTTP URLs
- Browsers block mixed content for security

## Root Cause

Laravel's `route()` helper generates URLs based on `APP_URL`. If `APP_URL` is set to HTTP or not set correctly, it generates HTTP URLs.

## Solution 1: Set APP_URL to HTTPS in Railway

**Most Important:** Set this in Railway Dashboard → Variables:

```env
APP_URL=https://shop-phone-production.up.railway.app
```

Make sure:
- ✅ Uses `https://` not `http://`
- ✅ No trailing slash
- ✅ Matches your exact Railway domain

## Solution 2: Force HTTPS in Laravel

I've updated the checkout page to use `url()` helper which respects the current request protocol, but you should still set `APP_URL` correctly.

## Solution 3: Use Protocol-Relative URLs (Not Recommended)

You could use `//domain.com` but this is deprecated and not recommended.

## After Fixing

1. **Set APP_URL in Railway:**
   ```
   APP_URL=https://shop-phone-production.up.railway.app
   ```

2. **Clear config cache:**
   ```bash
   railway run php artisan config:clear
   ```

3. **Restart Railway service** (or wait for auto-redeploy)

4. **Test checkout again**

## Verify APP_URL is Set

Check in Railway:
```bash
railway run php artisan tinker
```

Then:
```php
config('app.url'); // Should return https://shop-phone-production.up.railway.app
url('/'); // Should return HTTPS URL
```

## Additional Notes

- The code has been updated to use `url()` helper which is more reliable
- Always use `url(route('name'))` instead of just `route('name')` for AJAX calls
- Make sure all external resources (images, scripts) also use HTTPS

