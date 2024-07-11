<?php
namespace Database\Seeders;
use App\Models\Comment;
use Database\Factories\CommentFactory;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public static $count = 1500;
    public static $chunk = 200;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommentFactory::new()->count(self::$chunk)->create();
    }
}
