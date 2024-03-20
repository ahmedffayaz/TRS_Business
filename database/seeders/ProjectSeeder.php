<?php

namespace Database\Seeders;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Database\Factories\ProjectFactory;
use Illuminate\Support\Facades\Schema;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Project::truncate();
        Schema::enableForeignKeyConstraints();

        ProjectFactory::new()->count(300)->create()->each(function ($project) {
            $itrations = mt_rand(0, 2);
            $ids = [];
            for ($i = 0; $i < $itrations; $i++) {
                $ids = mt_rand(1, 11);
            }

            $project->members()->sync($ids);
        });
    }
}
