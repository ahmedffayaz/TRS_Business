<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Business;
use Illuminate\Support\Str;
use App\Enums\Business\BusinessType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->company();
        $slug = Str::slug($name);
        return [
            'name' => $name,
            'slug' => $slug,
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'country_id' => $this->faker->randomElement(Country::pluck('id')),
            'invoice_prefix' => $this->faker->randomElement(['trs_', 'new_trs_', 'dev_pro_', 'new_com_']),
            'invoice_serial' => $this->faker->randomElement(['hjkds69s', '78sdhjd', '26ds32', '4ds4ds'])
        ];
    }
}
