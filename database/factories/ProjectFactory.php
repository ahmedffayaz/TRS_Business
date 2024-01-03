<?php

namespace Database\Factories;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

use Faker\Factory as Faker;


class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $faker = Faker::create();
        $companyId = $faker->numberBetween(1, 11);
        $clientId = $faker->numberBetween(1, 11);
        $isFixed = $faker->boolean(40);
    
        return [
            'company_id' => $companyId,
            'client_company_id' => $companyId,
            'client_id' => $clientId,
            'name' => $faker->catchPhrase,
            'start_date' => Carbon::now()->subDays($faker->numberBetween(0, 14))->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays($faker->numberBetween(7, 365))->format('Y-m-d'),
            'status' => $faker->randomElement([
                'pending',
                'in-progress',
                'delivered',
            ]),
            'description' => $faker->text(100),
            'budget' => $isFixed ? $faker->numberBetween(300.0, 10000.0) : null,
            'currency' => $faker->randomElement([
                'USD',
                'EURO',
            ]),
            'hourly_rate' => $isFixed ? null : $faker->numberBetween(8.0, 14.0),
            'type' => $isFixed ? 'fixed' : 'hourly',
            'nature' => $faker->randomElement([
                'fixed',
                'weekly',
                'monthly',
            ]),
        ];
    }
}
