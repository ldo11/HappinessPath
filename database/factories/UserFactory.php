<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'dob' => fake()->date(),
            'disc_type' => fake()->randomElement(['D', 'I', 'S', 'C']),
            'role' => 'user',
            'spiritual_preference' => fake()->randomElement(['buddhist', 'taoist', 'christian', 'muslim', 'hindu', 'none']),
            'onboarding_status' => fake()->randomElement(['pending', 'completed']),
            'start_pain_level' => fake()->numberBetween(1, 10),
            'city' => fake()->city(),
            'district' => fake()->state(),
            'country' => fake()->country(),
            'geo_privacy' => fake()->boolean(),
            'locale' => 'en',
            'buddy_id' => null,
            'language' => 'en',
            'religion' => fake()->randomElement(['buddhist', 'taoist', 'christian', 'muslim', 'hindu', 'none']),
            'is_available' => fake()->boolean(80), // 80% chance of being available
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'is_available' => true,
        ]);
    }

    public function consultant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'consultant',
            'is_available' => true,
        ]);
    }

    public function translator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'translator',
            'is_available' => true,
        ]);
    }
}
