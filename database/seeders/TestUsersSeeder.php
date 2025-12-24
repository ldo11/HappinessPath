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
        User::firstOrCreate(['email' => 'admin@happiness.test'], [
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
        User::updateOrCreate(['email' => 'user@happiness.test'], [
            'name' => 'Regular User',
            'email' => 'user@happiness.test',
            'password' => Hash::make('123456'),
            'role' => 'user',
            'city' => 'Hanoi',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 7,
            'geo_privacy' => true,
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        // Create translator user (volunteer role)
        User::updateOrCreate(['email' => 'translator@happiness.test'], [
            'name' => 'Translator User',
            'email' => 'translator@happiness.test',
            'password' => Hash::make('123456'),
            'role' => 'translator',
            'city' => 'Remote',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 1,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $this->command->info('Test users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@happiness.test / 123456');
        $this->command->info('User: user@happiness.test / 123456');
        $this->command->info('Translator: translator@happiness.test / 123456');
    }
}
