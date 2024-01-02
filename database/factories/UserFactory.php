<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Enums\User\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'alternative_email' => $this->faker->unique()->safeEmail(),
            'designation' => $this->faker->randomElement(['Associate Software Engineer', 'Software Engineer', 'SQA Engineer', 'Graphic Designer', 'Project Manager']),
            'phone' => $this->faker->phoneNumber(),
            'alternative_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'salary' => $this->faker->randomNumber(5),
            'currency' => 'USD',
            'avatar' => $this->faker->imageUrl(225, 225, 'avatar'),
            'is_active' => $this->faker->randomElement([UserStatus::ACTIVE->value, UserStatus::INACTIVE->value]),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
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
}
