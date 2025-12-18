<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use environment variables if set, otherwise use defaults
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123');
        $adminName = env('ADMIN_NAME', 'Admin User');

        // Check if user already exists
        $existingUser = User::where('email', $adminEmail)->first();

        if ($existingUser) {
            // Update existing user to admin
            $existingUser->role = 'admin';
            if (env('ADMIN_PASSWORD')) {
                // Only update password if ADMIN_PASSWORD is set in env
                $existingUser->password = Hash::make($adminPassword);
            }
            $existingUser->save();
            $this->command->info("✅ Updated existing user to admin: {$adminEmail}");
        } else {
            // Create new admin user
            User::create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin'
            ]);
            $this->command->info("✅ Created admin user: {$adminEmail}");
            $this->command->warn("⚠️  Default password: admin123 - Please change this after first login!");
        }
    }
}

