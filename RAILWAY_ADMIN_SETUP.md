# How to Create Admin User in Railway

There are several ways to create an admin user in Railway. Choose the method that works best for you.

## Method 1: Using Railway CLI (Recommended)

### Step 1: Install Railway CLI
If you haven't already, install the Railway CLI:
```bash
npm i -g @railway/cli
```

### Step 2: Login to Railway
```bash
railway login
```

### Step 3: Link to your project
```bash
railway link
```

### Step 4: Run Tinker via Railway CLI
```bash
railway run php artisan tinker
```

### Step 5: Create Admin User
Once in tinker, run:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('your-secure-password'),
    'role' => 'admin'
]);
```

Or if a user already exists, update them:
```php
$user = App\Models\User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->save();
```

## Method 2: Using Railway Database Dashboard

### Step 1: Access Railway Dashboard
1. Go to [railway.app](https://railway.app)
2. Select your project
3. Click on your database service

### Step 2: Open Database Query Interface
1. Click on the "Query" tab or "Data" tab
2. You can run SQL queries directly

### Step 3: Create Admin User via SQL
```sql
-- First, check if user exists
SELECT * FROM users WHERE email = 'admin@example.com';

-- If user exists, update role
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';

-- If user doesn't exist, create one (you'll need to hash the password first)
-- Note: You'll need to generate a password hash. Use Method 1 or 3 for this.
```

## Method 3: Create Artisan Command (One-time Setup)

### Step 1: Create a Custom Artisan Command
Create a file: `app/Console/Commands/CreateAdminUser.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {email} {password} {name=Admin}';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name');

        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            if ($this->confirm('Do you want to update this user to admin?')) {
                $user = User::where('email', $email)->first();
                $user->role = 'admin';
                $user->save();
                $this->info("User {$email} is now an admin!");
            }
            return;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin'
        ]);

        $this->info("Admin user created successfully!");
        $this->info("Email: {$email}");
        $this->info("Name: {$name}");
    }
}
```

### Step 2: Run via Railway CLI
```bash
railway run php artisan admin:create admin@example.com "your-password" "Admin Name"
```

## Method 4: Using Railway Environment Variables + Seeder

### Step 1: Create a Seeder
Create `database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password');
        $adminName = env('ADMIN_NAME', 'Admin');

        if (User::where('email', $adminEmail)->exists()) {
            $user = User::where('email', $adminEmail)->first();
            $user->role = 'admin';
            $user->save();
            $this->command->info("Updated existing user to admin: {$adminEmail}");
        } else {
            User::create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin'
            ]);
            $this->command->info("Created admin user: {$adminEmail}");
        }
    }
}
```

### Step 2: Add Environment Variables in Railway
1. Go to Railway Dashboard → Your Project → Variables
2. Add these variables:
   - `ADMIN_EMAIL=admin@example.com`
   - `ADMIN_PASSWORD=your-secure-password`
   - `ADMIN_NAME=Admin`

### Step 3: Run the Seeder
```bash
railway run php artisan db:seed --class=AdminUserSeeder
```

## Quick Reference: Update Existing User to Admin

If you already have a user account and just want to make them admin:

### Via Tinker:
```bash
railway run php artisan tinker
```
```php
$user = App\Models\User::where('email', 'your-email@example.com')->first();
$user->role = 'admin';
$user->save();
```

### Via SQL:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

## Important Notes

- The `role` column must be set to `'admin'` (string, lowercase)
- Default role is `'user'` if not specified
- After creating the admin, you can login at: `https://your-app.railway.app/admin/login`
- Make sure to use a strong password for production!

## Troubleshooting

If you get "Target class does not exist" errors:
- Make sure you've run `composer install` in Railway
- Check that migrations have run: `railway run php artisan migrate`

If the admin role doesn't work:
- Verify the user has `role = 'admin'` in the database
- Check that the AdminMiddleware is properly registered (it should be in `bootstrap/app.php`)

