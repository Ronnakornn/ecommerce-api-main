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
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'remember_token' => Str::random(10),
            'status' => 'active',
            'user_info' => [
                'phone' => '0' . fake()->randomNumber(9, true), //0854412541
                'address' => fake()->address(), // '8888 Cummings Vista Apt. 101, Susanbury, NY 95473'
                'tax_id' => fake()->numerify('#############')  // '1874521021456'
            ],
            'user_img' => 'https://i.pravatar.cc/300?img=,'. rand(1, 100),
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

    public function company(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->company,
                'user_role' => 'company',
            ];
        });
    }

    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->name,
                'user_role' => 'admin',
            ];
        });
    }

    public function customer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => fake()->name,
                'user_role' => 'customer',
            ];
        });
    }

}
