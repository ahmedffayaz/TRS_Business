<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Project;

use Faker\Factory as Faker;
use App\Enums\Task\TaskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;


class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $faker = Faker::create();
        $isCompleted = $faker->boolean(20);
        $completeDate = Carbon::now()->subDays($faker->numberBetween(0, 14))->format('Y-m-d');

        // Retrieve a random project along with a random user associated with that project
        $project = Project::inRandomOrder()->with('members')->first();
        $projectMembersIds = $project?->members->pluck('id')->toArray();

        return [
            'name' => $faker->sentence($faker->numberBetween(3, 6)),
            'description' => $faker->realText(150),
            'user_id' => $faker->randomElement($projectMembersIds),
            'project_id' => $project->id,
            'priority' => $faker->randomElement([
                TaskPriority::LOW->value,
                TaskPriority::MEDIUM->value,
                TaskPriority::HIGH->value
            ]),
            'start_date' => Carbon::now()->subDays($faker->numberBetween(0, 14))->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays($faker->numberBetween(7, 365))->format('Y-m-d'),
            'completed_at' => $isCompleted ? $completeDate : null,
            'billed_at' => $isCompleted ? $completeDate : null,
        ];
    }
}
