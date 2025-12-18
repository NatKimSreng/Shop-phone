<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixAdminLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:fix {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix admin user - create or update with new password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $user = User::where('email', $email)->first();
        
        if ($user) {
            // Update existing user
            $user->password = Hash::make($password);
            $user->role = 'admin';
            $user->save();
            
            $this->info("✅ Updated user:");
            $this->line("   Email: {$user->email}");
            $this->line("   Name: {$user->name}");
            $this->line("   Role: {$user->role}");
            $this->line("   Password: Reset to provided password");
        } else {
            // Create new user
            $user = User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin'
            ]);
            
            $this->info("✅ Created new admin user:");
            $this->line("   Email: {$user->email}");
            $this->line("   Name: {$user->name}");
            $this->line("   Role: {$user->role}");
        }
        
        $this->newLine();
        $this->info("🔑 Login Credentials:");
        $this->line("   Email: {$email}");
        $this->line("   Password: {$password}");
        $this->newLine();
        $this->info("💡 Next steps:");
        $this->line("   1. Make sure APP_URL is set in Railway environment variables");
        $this->line("   2. Set SESSION_SECURE_COOKIE=true");
        $this->line("   3. Clear config cache: php artisan config:clear");
        $this->line("   4. Try logging in at: /admin/login");
        
        return 0;
    }
}

