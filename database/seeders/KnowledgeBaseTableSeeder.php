<?php

namespace Database\Seeders;
use App\Models\Role;
use App\Models\KnowledgeBase;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class knowledgeBaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::get()->pluck('id');

        $faker = Faker::create();
        foreach (range(1,20) as $index) {
            $knowledgeBase = KnowledgeBase::create([
                'keywords' => $faker->randomElement(['General', 'Web', 'Mobile', 'Interview']),
                'question' => $faker->text(20).'?',
                'answer' => $faker->text(100),
            ]);
            $knowledgeBase->roles()->attach($faker->randomElement($roles));
        };
    }
}