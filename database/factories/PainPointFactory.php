<?php

namespace Database\Factories;

use App\Models\PainPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PainPoint>
 */
class PainPointFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PainPoint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'category' => fake()->randomElement(['mind', 'wisdom', 'body']),
            'icon' => fake()->randomElement(['heart', 'brain', 'body', 'soul']),
            'description' => fake()->paragraph(2),
            'icon_url' => fake()->imageUrl(64, 64),
        ];
    }
}
