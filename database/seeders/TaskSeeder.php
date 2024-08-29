<?php

namespace Database\Seeders;
use App\Models\Task;
use Database\Factories\TaskFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TaskSeeder extends Seeder
{
    public static $count = 60;
    public static $chunk = 20;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Schema::enableForeignKeyConstraints();

        $total = self::$count;
        $chunkSize = self::$chunk;

        while ($total > 0) {
            $currentChunk = $total >= $chunkSize ? $chunkSize : $total;
            TaskFactory::new()->count($currentChunk)->create();
            $total -= $currentChunk;
        }
    }
}
