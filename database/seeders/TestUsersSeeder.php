<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = (string) (env('TEST_USER_PASSWORD') ?: '15987536245');

        // Create admin user
        User::firstOrCreate(['email' => 'admin@happiness.test'], [
            'name' => 'Admin User',
            'email' => 'admin@happiness.test',
            'password' => Hash::make($defaultPassword),
            'role' => 'admin',
            'language' => 'en',
            'city' => 'Ho Chi Minh City',
            'spiritual_preference' => 'buddhism',
            'start_pain_level' => 3,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        // Create regular user (member)
        User::updateOrCreate(['email' => 'user@happiness.test'], [
            'name' => 'Regular User',
            'email' => 'user@happiness.test',
            'password' => Hash::make($defaultPassword),
            'role' => 'user',
            'language' => 'en',
            'city' => 'Hanoi',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 7,
            'geo_privacy' => true,
            'email_verified_at' => now(),
            'onboarding_status' => 'new',
        ]);

        // Create translator user (volunteer role)
        User::updateOrCreate(['email' => 'translator@happiness.test'], [
            'name' => 'Translator User',
            'email' => 'translator@happiness.test',
            'password' => Hash::make($defaultPassword),
            'role' => 'translator',
            'language' => 'en',
            'city' => 'Remote',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 1,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        // Create consultant user
        User::updateOrCreate(['email' => 'consultant@example.com'], [
            'name' => 'Consultant',
            'email' => 'consultant@example.com',
            'password' => Hash::make((string) (env('CONSULTANT_SEED_PASSWORD') ?: $defaultPassword)),
            'role' => 'consultant',
            'city' => 'Remote',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 1,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        if ($this->command) {
            $this->command->info('Test users created successfully!');
            $this->command->info('Login credentials:');
            $this->command->info('Admin: admin@happiness.test / ' . $defaultPassword);
            $this->command->info('User: user@happiness.test / ' . $defaultPassword);
            $this->command->info('Translator: translator@happiness.test / ' . $defaultPassword);
            $this->command->info('Consultant: consultant@example.com / ' . ((string) (env('CONSULTANT_SEED_PASSWORD') ?: $defaultPassword)));
        }
    }
}
