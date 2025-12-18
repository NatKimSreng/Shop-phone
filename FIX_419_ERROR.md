# Fix 419 PAGE EXPIRED Error

The 419 error occurs when CSRF tokens don't match, usually due to session/cookie configuration issues in production.

## Quick Fix: Set These Environment Variables in Railway

Go to Railway Dashboard → Your Service → Variables, and add/update:

```env
APP_URL=https://your-app.railway.app
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DRIVER=database
```

## Detailed Explanation

### Required Environment Variables

1. **APP_URL** - Must match your Railway domain exactly
   ```
   APP_URL=https://shop-phone-production.up.railway.app
   ```

2. **SESSION_SECURE_COOKIE** - Must be `true` for HTTPS
   ```
   SESSION_SECURE_COOKIE=true
   ```

3. **SESSION_SAME_SITE** - Set to `lax` or `none` (for cross-site)
   ```
   SESSION_SAME_SITE=lax
   ```

4. **SESSION_DRIVER** - Use `database` for production
   ```
   SESSION_DRIVER=database
   ```

### Verify Sessions Table Exists

Make sure the sessions table was created by migrations:

```bash
railway run php artisan migrate:status
```

If sessions table doesn't exist, it should be created by the cache migration. If not, run:

```bash
railway run php artisan session:table
railway run php artisan migrate
```

## After Setting Variables

1. **Clear config cache:**
   ```bash
   railway run php artisan config:clear
   ```

2. **Restart your Railway service** (or wait for auto-redeploy)

3. **Clear browser cookies** for your Railway domain

4. **Try logging in again**

## Alternative: Use Cookie Session Driver (Temporary Fix)

If database sessions don't work, you can temporarily use cookie driver:

```env
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

**Note:** Cookie driver has size limitations and is less secure, but works for testing.

## Troubleshooting

### Still Getting 419 Error?

1. **Check APP_URL matches exactly:**
   - No trailing slash
   - Use `https://` not `http://`
   - Match the exact domain from Railway

2. **Check browser console for cookie errors:**
   - Open DevTools → Application → Cookies
   - Verify session cookie is being set
   - Check if cookie has `Secure` and `SameSite` attributes

3. **Verify CSRF token in form:**
   - View page source
   - Look for `<input type="hidden" name="_token" value="...">`
   - Should be present in the login form

4. **Check Railway logs:**
   ```bash
   railway logs
   ```
   Look for session-related errors

## Common Causes

1. ❌ **APP_URL not set or incorrect**
2. ❌ **SESSION_SECURE_COOKIE not set to true (for HTTPS)**
3. ❌ **Sessions table doesn't exist in database**
4. ❌ **Cookie domain mismatch**
5. ❌ **Browser blocking cookies (privacy settings)**

