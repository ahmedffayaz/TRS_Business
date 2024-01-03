<?php

namespace Database\Seeders;
use App\Models\Project;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProjectFactory::new()->count(10)->create()->each(function ($project) {
            $itrations = mt_rand(0, 2);
            $ids = [];
            for ($i = 0; $i < $itrations; $i++) {
                $ids = mt_rand(1, 11);
            }

            $project->members()->sync($ids);
        });
    }
}
