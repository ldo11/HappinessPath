<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = 'password';

        // Create admin user
        User::updateOrCreate(['email' => 'admin@happiness.test'], [
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
            'password' => Hash::make($defaultPassword),
            'role' => 'consultant',
            'city' => 'Remote',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 1,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        User::updateOrCreate(['email' => 'consultant1@test.com'], [
            'name' => 'Consultant One',
            'email' => 'consultant1@test.com',
            'password' => Hash::make($defaultPassword),
            'role' => 'consultant',
            'city' => 'Remote',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 1,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        User::updateOrCreate(['email' => 'consultant2@test.com'], [
            'name' => 'Consultant Two',
            'email' => 'consultant2@test.com',
            'password' => Hash::make($defaultPassword),
            'role' => 'consultant',
            'city' => 'Remote',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 1,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        User::updateOrCreate(['email' => 'user_vi@happiness.test'], [
            'name' => 'Vietnamese User',
            'email' => 'user_vi@happiness.test',
            'password' => Hash::make($defaultPassword),
            'role' => 'user',
            'language' => 'vi',
            'city' => 'Da Nang',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 5,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        User::updateOrCreate(['email' => 'user_de@happiness.test'], [
            'name' => 'German User',
            'email' => 'user_de@happiness.test',
            'password' => Hash::make($defaultPassword),
            'role' => 'user',
            'language' => 'de',
            'city' => 'Berlin',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 5,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        User::updateOrCreate(['email' => 'user_kr@happiness.test'], [
            'name' => 'Korean User',
            'email' => 'user_kr@happiness.test',
            'password' => Hash::make($defaultPassword),
            'role' => 'user',
            'language' => 'kr',
            'city' => 'Seoul',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 5,
            'geo_privacy' => false,
            'email_verified_at' => now(),
            'onboarding_status' => 'test_completed',
        ]);

        User::updateOrCreate(['email' => 'user_en@happiness.test'], [
            'name' => 'English User',
            'email' => 'user_en@happiness.test',
            'password' => Hash::make($defaultPassword),
            'role' => 'user',
            'language' => 'en',
            'city' => 'New York',
            'spiritual_preference' => 'secular',
            'start_pain_level' => 5,
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
            $this->command->info('Consultant: consultant@example.com / ' . $defaultPassword);
            $this->command->info('Consultant1: consultant1@test.com / ' . $defaultPassword);
            $this->command->info('Consultant2: consultant2@test.com / ' . $defaultPassword);
            $this->command->info('User VI: user_vi@happiness.test / ' . $defaultPassword);
            $this->command->info('User DE: user_de@happiness.test / ' . $defaultPassword);
            $this->command->info('User KR: user_kr@happiness.test / ' . $defaultPassword);
            $this->command->info('User EN: user_en@happiness.test / ' . $defaultPassword);
        }
    }
}
