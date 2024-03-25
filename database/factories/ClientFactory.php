<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Business;
use Illuminate\Support\Str;
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
        $currencies = currencies();
        $currencyKeys = array_keys($currencies);
        shuffle($currencyKeys); // Shuffle the currency keys array to randomize the order
        $randomCurrency = $currencyKeys[0]; // Select the first currency key after shuffling

        $name = $this->faker->unique()->company();
        $slug = Str::slug($name);

        return [
            'business_id' => $this->faker->randomElement(Business::pluck('id')),
            'name' => $name,
            'slug' => $slug,
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country_id' => $this->faker->randomElement(Country::pluck('id')),
            'postal_code' => $this->faker->postcode(),
            'rate_per_hour' => $this->faker->randomElement(['25', '30', '20', '40']),
            'rate_unit' => $randomCurrency, // Assign the randomly selected currency
            'note' => $this->faker->paragraph()
        ];
    }
}
