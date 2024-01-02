<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => $this->faker->randomElement(Company::pluck('id')),
            'name' => $this->faker->name(),
            'street_address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country_id' => $this->faker->randomElement(Country::pluck('id')),
            'postal_code' => $this->faker->postcode(),
            'rate_per_hour' => $this->faker->randomElement(['25', '30', '20', '40']),
            'rate_unit' => 'USD',
            'note' => $this->faker->paragraph()
        ];
    }
}
