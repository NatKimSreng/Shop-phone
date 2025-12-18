# Troubleshooting Admin Login Issues

## Quick Fix: Verify and Fix Admin User Using Tinker

Run this in Railway:

```bash
railway run php artisan tinker
```

Then paste these commands one by one:

```php
// 1. Find the user
$user = App\Models\User::where('email', 'kimsreng@gmail.com')->first();

// 2. Check if user exists
if (!$user) {
    echo "User not found!\n";
} else {
    echo "User found!\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . ($user->role ?? 'not set') . "\n";
    echo "Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No') . "\n";
}

// 3. Test password (replace 'admin123' with your actual password)
Hash::check('admin123', $user->password); // Should return true

// 4. Fix role if needed
$user->role = 'admin';
$user->save();
echo "Role updated to admin\n";

// 5. Reset password if needed (replace 'newpassword123' with desired password)
$user->password = Hash::make('newpassword123');
$user->save();
echo "Password reset to: newpassword123\n";
```

## Alternative: Use admin:create Command

If the user doesn't exist or you want to recreate it:

```bash
railway run php artisan admin:create kimsreng@gmail.com "your-password" "Admin"
```

## Fix Command Discovery Issue

If `admin:verify` command is not found, clear cache first:

```bash
railway run php artisan config:clear
railway run php artisan cache:clear
railway run composer dump-autoload
```

Then try again:
```bash
railway run php artisan admin:verify kimsreng@gmail.com
```

## Common Login Issues

### Issue 1: "Invalid credentials"
- **Cause:** Wrong password or password hash mismatch
- **Fix:** Reset password using tinker (see above)

### Issue 2: Login works but redirected to home instead of admin panel
- **Cause:** User role not set to 'admin'
- **Fix:** Set role using tinker: `$user->role = 'admin'; $user->save();`

### Issue 3: "You do not have permission to access this area"
- **Cause:** User role is not 'admin'
- **Fix:** Same as Issue 2

### Issue 4: Session/cookie issues
- **Fix:** Clear browser cookies, try incognito mode, or check SESSION_DRIVER in Railway environment variables

## Verify Login Credentials

After fixing, test login with:
- **URL:** `https://your-app.railway.app/admin/login`
- **Email:** `kimsreng@gmail.com`
- **Password:** The password you set (or `admin123` if using default)

