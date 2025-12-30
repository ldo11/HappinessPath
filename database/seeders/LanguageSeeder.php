<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'vi', 'name' => 'Vietnamese', 'is_active' => true, 'is_default' => true],
            ['code' => 'en', 'name' => 'English', 'is_active' => true, 'is_default' => false],
            ['code' => 'de', 'name' => 'German', 'is_active' => true, 'is_default' => false],
            ['code' => 'kr', 'name' => 'Korean', 'is_active' => true, 'is_default' => false],
        ];

        foreach ($rows as $row) {
            Language::query()->updateOrCreate(
                ['code' => $row['code']],
                $row
            );
        }
    }
}
