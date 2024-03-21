<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Business;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Enums\Project\ProjectType;

use App\Enums\Project\ProjectNature;
use App\Enums\Project\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $faker = Faker::create();
        // Get all businesses with their clients
        $businesses = Business::with('clients')->get();

        // Choose a random business
        $business = $businesses->random();

        // Choose a random client from the selected business
        $client = $business->clients->random();

        $isFixed = $faker->boolean(40);

        // Access the enum values directly
        $statuses = [
            ProjectStatus::PENDING,
            ProjectStatus::INPROGRESS,
            ProjectStatus::DELIVERED,
        ];

        $natures = [
            ProjectNature::FIXED,
            ProjectType::HOURLY,
            ProjectNature::WEEKLY,
        ];

        $projectName = $faker->unique()->catchPhrase;

        return [
            'business_id' => $business->id,
            'client_id' => $client->id,
            'name' => $projectName,
            'slug' => Str::slug($projectName),
            'start_date' => Carbon::now()->subDays($faker->numberBetween(0, 14))->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays($faker->numberBetween(7, 365))->format('Y-m-d'),
            'status' => $faker->randomElement($statuses)->value,
            'description' => $faker->text(100),
            'budget' => $isFixed ? $faker->numberBetween(300.0, 10000.0) : null,
            'currency' => $faker->randomElement(['EURO', 'USD', 'PKR', 'Pound', 'GBP', 'CAD']),
            'hourly_rate' => $isFixed ? null : $faker->numberBetween(8.0, 14.0),
            'type' => $isFixed ? ProjectType::FIXED->value : ProjectType::HOURLY->value,
            'nature' => $faker->randomElement($natures)->value,
            'is_auto_archived' => $faker->randomElement([true, false])
        ];
    }
}
