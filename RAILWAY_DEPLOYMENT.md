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

### Option 3: Use Railway Deploy Hooks (Advanced)

You can add a deploy hook in `railway.toml`:

```toml
[deploy]
startCommand = "php artisan serve --host=0.0.0.0 --port=$PORT"
restartPolicyType = "ON_FAILURE"
restartPolicyMaxRetries = 10

[deploy.hooks]
postDeploy = "php artisan db:seed --class=AdminUserSeeder"
```

However, this will run on EVERY deployment, which might not be desired.

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
2. **Wait for deployment to complete**
3. **Run seeder via CLI:**
   ```bash
   railway run php artisan db:seed --class=AdminUserSeeder
   ```
4. **Login at:** `https://your-app.railway.app/admin/login`
   - Email: `admin@example.com` (or your ADMIN_EMAIL)
   - Password: `admin123` (or your ADMIN_PASSWORD)

## Troubleshooting

### Build Fails with Seeder
- **Solution:** Remove seeder from build command, run it manually after deployment

### Seeder Says "Database connection failed"
- **Solution:** Wait a few minutes after deployment, then try again. Database might still be initializing.

### Seeder Runs But User Not Created
- **Solution:** Check Railway logs for errors. Verify environment variables are set correctly.

### Can't Login After Creating Admin
- **Solution:** Verify the user was created:
  ```bash
  railway run php artisan tinker
  ```
  Then:
  ```php
  User::where('email', 'admin@example.com')->first();
  ```
  Check that `role = 'admin'`

