<?php

namespace Database\Factories;

use App\Enums\User\AccountType;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Business;
use App\Models\Client;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;


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
            'account_type' => AccountType::CLIENT->value,
            'client_id' => $this->faker->randomElement(Client::pluck('id'))
        ];
    }
}


