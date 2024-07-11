<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Keyword;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as FakerGenerator;
use Illuminate\Support\Facades\Schema;
use Database\Factories\KnowledgeBaseFactory;
use Database\Factories\KnowledgeBaseCategoryFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KnowledgeBaseCategorySeeder extends Seeder
{

    protected $faker;

    public function __construct(FakerGenerator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        $this->truncateTables(['knowledge_bases', 'knowledge_base_categories', 'knowledge_base_category_role', 'keyword_knowledge_base']);
        Schema::enableForeignKeyConstraints();

        $roles = Role::pluck('id')->toArray();

        $knowledgeBaseCategoryFactory = KnowledgeBaseCategoryFactory::new()->count(30)->create();

        $knowledgeBaseCategoryFactory->each(function($category) use ($roles) {
            KnowledgeBaseFactory::new()->count(5)->create()->each(function ($knowledgeBase) {
                $keyword = Keyword::create([
                    'name' => $this->faker->word
                ]);
                $knowledgeBase->keywords()->attach($keyword->id);
            });

            $category->roles()->attach($roles);
        });
    }

    // Truncate tables
    protected function truncateTables(array $tables)
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
