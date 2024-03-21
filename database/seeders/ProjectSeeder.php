<?php

namespace Database\Seeders;
use App\Models\User;
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

        // Get all user IDs
        $userIds = User::pluck('id')->toArray();


        // Create projects
        ProjectFactory::new()->count(300)->create()->each(function ($project) use ($userIds) {
            $randomUserIds = mt_rand(1, count($userIds));
            // Attach users as members to the project
            $project->members()->attach($randomUserIds);
       });
    }
}
