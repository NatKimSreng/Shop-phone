# Quick Fix: Can't Login to Admin Panel

Follow these steps in order to fix your login issue.

## Step 1: Run Diagnostic Command

First, let's see what's wrong:

```bash
railway run php artisan admin:diagnose kimsreng@gmail.com
```

This will check:
- ✅ Database connection
- ✅ User exists
- ✅ User role is 'admin'
- ✅ Password is set
- ✅ Environment variables

## Step 2: Fix the User (If Needed)

If the diagnostic shows issues, fix them with:

```bash
railway run php artisan admin:fix kimsreng@gmail.com "your-new-password"
```

This will:
- Create the user if it doesn't exist
- Set role to 'admin'
- Reset the password to what you specify

## Step 3: Set Environment Variables in Railway

Go to **Railway Dashboard → Your Service → Variables** and add/update:

```env
APP_URL=https://shop-phone-production.up.railway.app
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_DRIVER=database
```

**Important:** Replace `shop-phone-production.up.railway.app` with your actual Railway domain (check your Railway dashboard for the exact URL).

## Step 4: Clear Cache

```bash
railway run php artisan config:clear
railway run php artisan cache:clear
```

## Step 5: Verify Sessions Table Exists

```bash
railway run php artisan migrate:status
```

If sessions table is missing, create it:

```bash
railway run php artisan session:table
railway run php artisan migrate
```

## Step 6: Test Login Setup (Optional)

Visit this URL in your browser to check the setup:

```
https://your-app.railway.app/test-login-setup?email=kimsreng@gmail.com
```

This will show you:
- If user exists
- If user is admin
- Current environment configuration

## Step 7: Try Logging In

1. **Clear your browser cookies** (or use incognito mode)
2. Go to: `https://your-app.railway.app/admin/login`
3. Enter:
   - **Email:** `kimsreng@gmail.com`
   - **Password:** The password you set in Step 2

## Common Issues & Solutions

### Issue: "419 PAGE EXPIRED"
**Solution:**
- Set `APP_URL` correctly (must match your Railway domain exactly)
- Set `SESSION_SECURE_COOKIE=true`
- Clear browser cookies

### Issue: "Invalid credentials"
**Solution:**
- Run: `railway run php artisan admin:fix kimsreng@gmail.com "newpassword"`
- Make sure you're using the exact password you set

### Issue: "You do not have permission"
**Solution:**
- User role is not 'admin'
- Run: `railway run php artisan admin:fix kimsreng@gmail.com "password"`

### Issue: User doesn't exist
**Solution:**
- Run: `railway run php artisan admin:fix kimsreng@gmail.com "password"`

## All-in-One Fix Command

If you want to do everything at once:

```bash
# 1. Fix/create admin user
railway run php artisan admin:fix kimsreng@gmail.com "MySecurePassword123"

# 2. Clear cache
railway run php artisan config:clear

# 3. Verify
railway run php artisan admin:diagnose kimsreng@gmail.com
```

Then set the environment variables in Railway dashboard and try logging in.

## Still Not Working?

1. **Check Railway logs:**
   ```bash
   railway logs
   ```
   Look for any errors related to sessions or authentication.

2. **Verify environment variables are set:**
   - Go to Railway Dashboard → Variables
   - Make sure `APP_URL`, `SESSION_SECURE_COOKIE` are set
   - Restart the service after setting variables

3. **Test the diagnostic route:**
   Visit: `https://your-app.railway.app/test-login-setup?email=kimsreng@gmail.com`
   This will show you exactly what's configured.

4. **Try a different browser or incognito mode** to rule out cookie issues.

