<?php

namespace Database\Seeders;
use App\Models\Task;
use Database\Factories\TaskFactory;
use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaskFactory::new()->count(5)->create();
    }
}
