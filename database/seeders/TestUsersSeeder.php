<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@happiness.test',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'city' => 'Ho Chi Minh City',
            'spiritual_preference' => 'buddhism',
            'start_pain_level' => 3,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        // Create regular user (member)
        User::create([
            'name' => 'Regular User',
            'email' => 'user@happiness.test',
            'password' => Hash::make('123456'),
            'role' => 'member',
            'city' => 'Hanoi',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 7,
            'geo_privacy' => true,
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        // Create volunteer user
        User::create([
            'name' => 'Volunteer User',
            'email' => 'volunteer@happiness.test',
            'password' => Hash::make('123456'),
            'role' => 'volunteer',
            'city' => 'Da Nang',
            'spiritual_preference' => 'christianity',
            'start_pain_level' => 5,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@test.com / 123456');
        $this->command->info('User: user@test.com / 123456');
        $this->command->info('Volunteer: volunteer@test.com / 123456');
    }
}
