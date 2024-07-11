<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;

use App\Models\Comment;
use Faker\Factory as Faker;
use App\Enums\Comment\CommentType;
use Illuminate\Database\Eloquent\Factories\Factory;


class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $faker = Faker::create();
        $isToday = $faker->boolean(60);
        $type = [
            CommentType::ASSIGNED,
            CommentType::ATTACHMENT,
            CommentType::COMMENT,
            CommentType::REMOVED,
            CommentType::TIME
        ];
        $tasks = Task::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();
        return [
            'description' => $faker->realText(150),
            'time' => $faker->numberBetween(3, 5) * 60,
            'type' => $faker->randomElement($type),
            'task_id' => $faker->randomElement($tasks),
            'from' => $faker->randomElement($users),
            'dated' => $isToday
                ? Carbon::now()->format('Y-m-d')
                : Carbon::now()->subDays($faker->numberBetween(0, 3))->format('Y-m-d'),
            'is_billable' => $faker->randomElement([0, 1])
        ];
    }
}
