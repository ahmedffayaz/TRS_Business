<?php

namespace Database\Factories;

use App\Models\Country;
use App\Enums\Company\CompanyType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'logo' => $this->faker->imageUrl(225, 225, 'avatar'),
            'street_address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country_id' => $this->faker->randomElement(Country::pluck('id')),
            'postal_code' => $this->faker->postcode(),
            'type' => CompanyType::PARENT->value,
        ];
    }
}
