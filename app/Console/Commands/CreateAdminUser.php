<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email} {password} {name=Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user or update existing user to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name');

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return 1;
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $this->warn("User with email {$email} already exists!");

            if ($this->confirm('Do you want to update this user to admin?', true)) {
                $existingUser->role = 'admin';

                // Optionally update password
                if ($this->confirm('Do you want to update the password?', false)) {
                    $existingUser->password = Hash::make($password);
                }

                // Optionally update name
                if ($name !== 'Admin' && $this->confirm('Do you want to update the name?', false)) {
                    $existingUser->name = $name;
                }

                $existingUser->save();
                $this->info("✅ User {$email} is now an admin!");
                return 0;
            } else {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Create new admin user
        try {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin'
            ]);

            $this->info("✅ Admin user created successfully!");
            $this->line("Email: {$email}");
            $this->line("Name: {$name}");
            $this->line("Role: admin");
            $this->newLine();
            $this->info("You can now login at: /admin/login");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed to create admin user: " . $e->getMessage());
            return 1;
        }
    }
}

