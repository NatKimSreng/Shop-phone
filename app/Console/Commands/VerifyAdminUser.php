<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class VerifyAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:verify {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify admin user exists and can login';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'kimsreng@gmail.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ User with email {$email} not found!");
            return 1;
        }
        
        $this->info("✅ User found:");
        $this->line("   ID: {$user->id}");
        $this->line("   Name: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->line("   Role: {$user->role ?? 'not set'}");
        $this->line("   Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No'));
        $this->line("   Password Hash: " . substr($user->password, 0, 20) . '...');
        
        // Test password verification
        $testPassword = $this->ask('Enter password to test (or press Enter to skip)', '');
        
        if ($testPassword) {
            if (Hash::check($testPassword, $user->password)) {
                $this->info("✅ Password verification: SUCCESS");
            } else {
                $this->error("❌ Password verification: FAILED");
                $this->warn("The password you entered does not match the stored hash.");
            }
        }
        
        // Check if role is set correctly
        if (!$user->isAdmin()) {
            $this->warn("⚠️  User is not an admin. Role is: '{$user->role}'");
            if ($this->confirm('Do you want to set this user as admin?', true)) {
                $user->role = 'admin';
                $user->save();
                $this->info("✅ User role updated to 'admin'");
            }
        }
        
        return 0;
    }
}

