<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'url' => 'https://www.youtube.com/watch?v=' . fake()->unique()->lexify('??????????'),
            'category' => fake()->randomElement(['body', 'mind', 'wisdom']),
            'language' => fake()->randomElement(['en', 'vi', 'de', 'kr']),
            'pillar_tag' => fake()->randomElement(['body', 'mind', 'wisdom']),
            'source_tag' => fake()->randomElement(['buddhism', 'christianity', 'science']),
            'pillar_tags' => [fake()->randomElement(['body', 'mind', 'wisdom'])],
            'source_tags' => [fake()->randomElement(['buddhism', 'christianity', 'science'])],
            'is_active' => true,
            'xp_reward' => fake()->numberBetween(5, 20),
        ];
    }
}
