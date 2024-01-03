<?php

namespace Database\Factories;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

use Faker\Factory as Faker;


class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $faker = Faker::create();
        $isToday = $faker->boolean(60);
        return [
            'description' => $faker->realText(150),
            'time' => $faker->numberBetween(3, 5) * 60,
            'type' => $faker->boolean(40) ? 'time' : 'comment',
            'task_id' => $faker->numberBetween(1, 4),
            'from' => $faker->numberBetween(1, 11),
            'dated' => $isToday
                ? Carbon::now()->format('Y-m-d')
                : Carbon::now()->subDays($faker->numberBetween(0, 3))->format('Y-m-d'),
        ];
    }
}
