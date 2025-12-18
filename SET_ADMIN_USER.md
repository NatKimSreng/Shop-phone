# How to Set a User as Admin

The database uses a `role` column (not `is_admin`). Here are the correct ways to set a user as admin:

## Method 1: Using Tinker (Recommended)

```bash
php artisan tinker
```

Then run:
```php
$user = App\Models\User::find(1); // Replace 1 with your user ID
$user->role = 'admin';
$user->save();
```

Or by email:
```php
$user = App\Models\User::where('email', 'admin@example.com')->first();
$user->role = 'admin';
$user->save();
```

## Method 2: Using SQL (Direct Database)

```sql
UPDATE users SET role = 'admin' WHERE id = 1;
```

Or by email:
```sql
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
```

## Method 3: Create New Admin User

```php
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('your-password'),
    'role' => 'admin'
]);
```

## Available Roles

- `'user'` - Regular user (default)
- `'admin'` - Administrator

## Important Notes

- The column is called `role`, NOT `is_admin`
- The value must be a string: `'admin'` or `'user'`
- Do NOT use: `$user->is_admin = 1` ❌
- Use instead: `$user->role = 'admin'` ✅

