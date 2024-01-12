<?php

namespace Database\Factories;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

use Faker\Factory as Faker;


class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $faker = Faker::create();
        $isCompleted = $faker->boolean(20);
        $completeDate = Carbon::now()->subDays($faker->numberBetween(0, 14))->format('Y-m-d');

        return [
            'name' => $faker->sentence($faker->numberBetween(3, 6)),
            'description' => $faker->realText(150),
            'user_id' => $faker->numberBetween(1, 11),
            'project_id' => $faker->numberBetween(1, 5),
            'priority' => $faker->randomElement([
                'low',
                'medium',
                'high',
            ]),
            'start_date' => Carbon::now()->subDays($faker->numberBetween(0, 14))->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays($faker->numberBetween(7, 365))->format('Y-m-d'),
            'completed_at' => $isCompleted ? $completeDate : null,
            'billed_at' => $isCompleted ? $completeDate : null,
        ];
    }
}
