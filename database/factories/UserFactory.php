<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // hard coded values to use this factory to run Tests
        return [
            'uuid'      => '96f26bc1-3cd2-4154-bec9-1df5c3026387',
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => 'rohu2187@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('rohan'),
            'avatar' => fake()->uuid(),
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'is_marketing' => 1,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
