<?php

namespace Database\Seeders;

use App\Models\PainPoint;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create pain points for testing
        $painPoints = [
            [
                'name' => 'Anxiety',
                'description' => 'Feeling of worry, nervousness, or unease',
                'category' => 'mind',
            ],
            [
                'name' => 'Depression',
                'description' => 'Persistent feeling of sadness and loss of interest',
                'category' => 'mind',
            ],
            [
                'name' => 'Stress',
                'description' => 'Physical or emotional tension',
                'category' => 'body',
            ],
            [
                'name' => 'Relationship Issues',
                'description' => 'Difficulties in personal relationships',
                'category' => 'wisdom',
            ],
            [
                'name' => 'Career Confusion',
                'description' => 'Uncertainty about professional direction',
                'category' => 'wisdom',
            ],
        ];

        foreach ($painPoints as $painPoint) {
            PainPoint::create($painPoint);
        }
    }
}
