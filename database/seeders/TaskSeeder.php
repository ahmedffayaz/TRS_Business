<?php

namespace Database\Seeders;
use App\Models\Task;
use Database\Factories\TaskFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TaskSeeder extends Seeder
{
    public static $count = 900;
    public static $chunk = 200;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Task::truncate();
        Schema::enableForeignKeyConstraints();

        TaskFactory::new()->count(self::$chunk)->create();
    }
}
