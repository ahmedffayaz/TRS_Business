<?php

namespace Database\Factories;

use App\Enums\User\AccountType;
use App\Enums\User\UserStatus;
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

        // Get all businesses with their clients
        $businesses = Business::with('clients')->get();

        // Choose a random business
        $business = $businesses->random();

        // Choose a random client from the selected business
        $client = $business->clients->random();

        return [
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'email' => $faker->unique()->email,
            'password' => '123456',
            'is_active' => UserStatus::ACTIVE->value,
            'phone' => '0000000000',
            'address' => 'Islamabad',
            'account_type' => AccountType::CLIENT->value,
            'business_id' => $business->id,
            'client_id' => $client->id
        ];
    }
}


