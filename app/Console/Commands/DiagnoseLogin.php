<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DiagnoseLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:diagnose {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose admin login issues comprehensively';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'kimsreng@gmail.com';

        $this->info("🔍 Diagnosing login issues for: {$email}");
        $this->newLine();

        // 1. Check database connection
        $this->info("1️⃣ Checking database connection...");
        try {
            DB::connection()->getPdo();
            $this->info("   ✅ Database connected");
        } catch (\Exception $e) {
            $this->error("   ❌ Database connection failed: " . $e->getMessage());
            return 1;
        }

        // 2. Check if users table exists
        $this->info("2️⃣ Checking users table...");
        if (!DB::getSchemaBuilder()->hasTable('users')) {
            $this->error("   ❌ Users table does not exist!");
            $this->warn("   Run: php artisan migrate");
            return 1;
        }
        $this->info("   ✅ Users table exists");

        // 3. Check if sessions table exists (if using database driver)
        $sessionDriver = Config::get('session.driver');
        $this->info("3️⃣ Checking sessions table (driver: {$sessionDriver})...");
        if ($sessionDriver === 'database') {
            if (!DB::getSchemaBuilder()->hasTable('sessions')) {
                $this->warn("   ⚠️  Sessions table does not exist!");
                $this->warn("   Run: php artisan session:table && php artisan migrate");
            } else {
                $this->info("   ✅ Sessions table exists");
            }
        } else {
            $this->info("   ℹ️  Using {$sessionDriver} driver (no table needed)");
        }

        // 4. Check user exists
        $this->info("4️⃣ Checking if user exists...");
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("   ❌ User not found!");
            $this->warn("   Create user with: php artisan admin:create {$email} 'password' 'Admin'");
            return 1;
        }

        $this->info("   ✅ User found:");
        $this->line("      ID: {$user->id}");
        $this->line("      Name: {$user->name}");
        $this->line("      Email: {$user->email}");
        $this->line("      Role: " . ($user->role ?? 'not set'));
        $this->line("      Is Admin: " . ($user->isAdmin() ? 'Yes ✅' : 'No ❌'));

        // 5. Check role
        if (!$user->isAdmin()) {
            $this->warn("   ⚠️  User is NOT an admin!");
            if ($this->confirm('   Fix role to admin?', true)) {
                $user->role = 'admin';
                $user->save();
                $this->info("   ✅ Role updated to admin");
            }
        }

        // 6. Check password hash
        $this->info("5️⃣ Checking password hash...");
        if (empty($user->password)) {
            $this->error("   ❌ Password is empty!");
            $this->warn("   Reset password with: php artisan admin:create {$email} 'newpassword' 'Admin'");
        } else {
            $this->info("   ✅ Password hash exists: " . substr($user->password, 0, 20) . '...');
        }

        // 7. Test password
        $this->info("6️⃣ Testing password...");
        $testPassword = $this->secret('   Enter password to test (or press Enter to skip)');

        if ($testPassword) {
            if (Hash::check($testPassword, $user->password)) {
                $this->info("   ✅ Password verification: SUCCESS");
            } else {
                $this->error("   ❌ Password verification: FAILED");
                $this->warn("   The password does not match!");
                if ($this->confirm('   Reset password?', true)) {
                    $newPassword = $this->secret('   Enter new password');
                    $user->password = Hash::make($newPassword);
                    $user->save();
                    $this->info("   ✅ Password reset successfully");
                }
            }
        }

        // 8. Check environment variables
        $this->info("7️⃣ Checking environment configuration...");
        $appUrl = env('APP_URL');
        $sessionSecure = env('SESSION_SECURE_COOKIE');
        $sessionSameSite = env('SESSION_SAME_SITE');
        $sessionDriver = env('SESSION_DRIVER');

        $this->line("   APP_URL: " . ($appUrl ?: 'not set ❌'));
        $this->line("   SESSION_SECURE_COOKIE: " . ($sessionSecure ?: 'not set (will auto-detect)'));
        $this->line("   SESSION_SAME_SITE: " . ($sessionSameSite ?: 'lax (default)'));
        $this->line("   SESSION_DRIVER: " . ($sessionDriver ?: 'database (default)'));

        if (!$appUrl) {
            $this->warn("   ⚠️  APP_URL not set! Set it to your Railway domain in environment variables.");
        }

        // 9. Summary and recommendations
        $this->newLine();
        $this->info("📋 Summary:");
        $this->line("   User exists: " . ($user ? 'Yes ✅' : 'No ❌'));
        $this->line("   Is Admin: " . ($user && $user->isAdmin() ? 'Yes ✅' : 'No ❌'));
        $this->line("   Has Password: " . ($user && !empty($user->password) ? 'Yes ✅' : 'No ❌'));
        $this->line("   APP_URL Set: " . ($appUrl ? 'Yes ✅' : 'No ❌'));

        $this->newLine();
        $this->info("💡 Recommendations:");

        if (!$user->isAdmin()) {
            $this->line("   1. Fix user role: php artisan admin:create {$email} 'password' 'Admin'");
        }

        if (!$appUrl) {
            $this->line("   2. Set APP_URL in Railway: https://your-app.railway.app");
        }

        $this->line("   3. Set SESSION_SECURE_COOKIE=true in Railway");
        $this->line("   4. Clear config cache: php artisan config:clear");
        $this->line("   5. Clear browser cookies and try again");

        return 0;
    }
}

