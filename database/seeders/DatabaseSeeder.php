<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Foundation
            LanguageSeeder::class,
            LanguageLineSeeder::class,

            // Users & Roles
            TestUsersSeeder::class,

            // Content & Logic
            PainPointSeeder::class,
            AssessmentSeeder::class,
            DailyMissionSeeder::class,
            VideoSeeder::class,

            // Translations (Last)
            TranslationSeeder::class,
        ]);
    }
}
