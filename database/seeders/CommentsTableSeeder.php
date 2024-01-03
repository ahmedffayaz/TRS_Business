<?php
namespace Database\Seeders;
use App\Models\Comment;
use Database\Factories\CommentFactory;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommentFactory::new()->count(5)->create();
    }
}
