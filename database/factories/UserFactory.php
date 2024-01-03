<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

use Faker\Factory as Faker;


class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $faker = Faker::create();
        return [
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $faker->unique()->email,
            'password' => '123456',
            'account_type' => 'active',
            'phone' => '0000000000',
            'address' => 'Islamabad',
            'company_id' => $faker->unique()->numberBetween(2, 11)
        ];
    }
}


