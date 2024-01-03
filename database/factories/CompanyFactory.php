<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

use Faker\Factory as Faker;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $faker = Faker::create();
        return [
            'name' => $faker->name(),
            'address' => $faker->address,
            'city' => $faker->city,
            'postal_code' => $faker->postcode,
            'country_id' => $faker->numberBetween(1, 3),
            'rate_per_hour' => $faker->numberBetween(6, 12),
            'rate_per_hour_unit' => $faker->randomElement(['USD', 'EURO', 'PKR', 'Pound', 'CAD']),
            'type' => $faker->randomElement([
                'business',
                'client',
            ]),
        ];
    }
}
