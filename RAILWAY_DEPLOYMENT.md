# Railway Deployment Guide

## Important: Don't Run Seeders During Build

**Seeders should NOT run during the build phase** because:
- Database connections may not be fully established
- Build environment is different from runtime environment
- It can cause build failures

## Recommended Build Command

In Railway, set your **Build Command** to:
```bash
composer install --no-dev && php artisan key:generate --force && php artisan migrate --force && php artisan config:clear
```

**Do NOT include** `php artisan db:seed` in the build command.

## How to Create Admin User After Deployment

### Option 1: Use Railway CLI (Recommended)

After your app is deployed, run:

```bash
railway run php artisan db:seed --class=AdminUserSeeder
```

Or use the custom command:

```bash
railway run php artisan admin:create admin@example.com "your-password"
```

### Option 2: Set Environment Variables and Run Seeder

1. In Railway Dashboard → Your Service → Variables, add:
   - `ADMIN_EMAIL=admin@example.com`
   - `ADMIN_PASSWORD=your-secure-password`
   - `ADMIN_NAME=Admin User`

2. Then run:
```bash
railway run php artisan db:seed --class=AdminUserSeeder
```

### Option 3: Use Railway Pre-Deploy Command (In Dashboard)

In Railway Dashboard → Your Service → Settings → Deploy:

1. **Pre-deploy command** (runs before container starts):
   ```
   php artisan db:seed --class=AdminUserSeeder
   ```
   ⚠️ **Important:** Do NOT use `railway run` - just use the command directly:
   - ✅ Correct: `php artisan db:seed --class=AdminUserSeeder`
   - ❌ Wrong: `railway run php artisan db:seed --class=AdminUserSeeder`

2. **Start command:**
   ```
   php artisan serve --host=0.0.0.0 --port=$PORT
   ```

**Note:** Pre-deploy commands run in the container environment, so you have direct access to `php artisan`. The `railway` CLI is only available when running commands from your local machine.

**Warning:** Pre-deploy runs on EVERY deployment. If you only want to create the admin user once, use Option 1 (manual) instead.

## Current Build Command Issue

If you're seeing build failures with:
```
php artisan migrate --force && php artisan config:clear && php artisan db:seed --class=AdminUserSeeder
```

**Fix:** Remove the seeder part:
```
php artisan migrate --force && php artisan config:clear
```

Then run the seeder manually after deployment.

## Quick Setup Steps

1. **Deploy your app** (with migrations in build command)
2. **Set required environment variables in Railway:**
   - `APP_URL=https://your-app.railway.app` (replace with your actual Railway URL)
   - `SESSION_SECURE_COOKIE=true`
   - `SESSION_SAME_SITE=lax`
   - `SESSION_DRIVER=database`
3. **Wait for deployment to complete**
4. **Run seeder via CLI:**
   ```bash
   railway run php artisan db:seed --class=AdminUserSeeder
   ```
5. **Clear config cache:**
   ```bash
   railway run php artisan config:clear
   ```
6. **Login at:** `https://your-app.railway.app/admin/login`
   - Email: `admin@example.com` (or your ADMIN_EMAIL)
   - Password: `admin123` (or your ADMIN_PASSWORD)

## Fix 419 PAGE EXPIRED Error

If you get a 419 error when logging in, see `FIX_419_ERROR.md` for detailed instructions. Quick fix:

1. Set `APP_URL` to your exact Railway domain (with https://)
2. Set `SESSION_SECURE_COOKIE=true`
3. Clear config cache and restart service

## Troubleshooting

### "railway: command not found" in Pre-Deploy Command
- **Problem:** You're using `railway run` in the pre-deploy command
- **Solution:** Remove `railway run` - pre-deploy commands run directly in the container
  - ✅ Use: `php artisan db:seed --class=AdminUserSeeder`
  - ❌ Don't use: `railway run php artisan db:seed --class=AdminUserSeeder`
- **Why:** The `railway` CLI is only available on your local machine, not inside Railway containers

### Build Fails with Seeder
- **Solution:** Remove seeder from build command, run it manually after deployment

### Seeder Says "Database connection failed"
- **Solution:** Wait a few minutes after deployment, then try again. Database might still be initializing.

### Seeder Runs But User Not Created
- **Solution:** Check Railway logs for errors. Verify environment variables are set correctly.

### Can't Login After Creating Admin
- **Solution 1:** Verify the user was created using tinker:
  ```bash
  railway run php artisan tinker
  ```
  Then:
  ```php
  $user = App\Models\User::where('email', 'kimsreng@gmail.com')->first();
  // Check user details
  $user->id;
  $user->name;
  $user->email;
  $user->role; // Should be 'admin'
  $user->isAdmin(); // Should return true
  
  // Test password (replace 'your-password' with actual password)
  Hash::check('your-password', $user->password); // Should return true
  
  // Fix role if needed
  $user->role = 'admin';
  $user->save();
  ```

- **Solution 2:** Use the verify command (after clearing cache):
  ```bash
  railway run php artisan config:clear
  railway run php artisan admin:verify kimsreng@gmail.com
  ```

- **Solution 3:** Reset password if login fails:
  ```bash
  railway run php artisan tinker
  ```
  Then:
  ```php
  $user = App\Models\User::where('email', 'kimsreng@gmail.com')->first();
  $user->password = Hash::make('newpassword123');
  $user->role = 'admin';
  $user->save();
  ```

