<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;
use App\Models\KnowledgeBase;
use Illuminate\Database\Seeder;
use App\Models\KnowledgeBaseTopic;
use Illuminate\Support\Facades\DB;
use Faker\Generator as FakerGenerator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KnowledgeBaseSeeder extends Seeder
{
    const NUMBER_OF_KNOWLEDGE_BASES = 20;
    const NUMBER_OF_TOPICS = 7;
    const NUMBER_OF_QAS = 5;

    protected $faker;

    public function __construct(FakerGenerator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $this->truncateTables(['knowledge_bases', 'knowledge_base_categories', 'knowledge_base_category_role', 'keyword_knowledge_base']);
        Schema::enableForeignKeyConstraints();

        $roles = Role::pluck('id')->toArray();
        $companies = Company::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();

        foreach (range(1, self::NUMBER_OF_KNOWLEDGE_BASES) as $index) {
            // Create knowledge base record
            $knowledgeBase = $this->createKnowledgeBase($companies, $users, $roles);

            // Add knowledge base topics
            foreach (range(1, self::NUMBER_OF_TOPICS) as $index) {
                $this->createKnowledgeBaseTopic($knowledgeBase);
            }
        }
    }

    // Truncate tables
    protected function truncateTables(array $tables)
    {
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    // Add knowledge base data in DB
    protected function createKnowledgeBase(array $companies, array $users, array $roles)
    {
        $name = substr($this->faker->paragraph(1), 0, 60);

        $knowledgeBase = KnowledgeBase::create([
            'created_by' => $this->faker->randomElement($users),
            'updated_by' => $this->faker->randomElement($users),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => substr($this->faker->paragraph(1), 0, 120)
        ]);

        // Attach randomly 0 to 6 companies
        $randomCompanies = $this->faker->randomElements($companies, $this->faker->numberBetween(0, 6));
        $knowledgeBase->companies()->attach($randomCompanies);

        // Assign role
        $knowledgeBase->roles()->attach($this->faker->randomElement($roles));

        return $knowledgeBase;
    }

    // Add knowledge base topics in DB
    protected function createKnowledgeBaseTopic(KnowledgeBase $knowledgeBase)
    {
        $name = substr($this->faker->paragraph(1), 0, 60);

        $knowledgeBaseTopic = $knowledgeBase->topics()->create([
            'name' => $name,
            'slug' => Str::slug($name)
        ]);

        // Add knowledge base questions and answers
        foreach (range(1, self::NUMBER_OF_QAS) as $index) {
            $this->createKnowledgeBaseQa($knowledgeBaseTopic);
        }
    }

    // Add knowledge base questions and answers in DB
    protected function createKnowledgeBaseQa(KnowledgeBaseTopic $knowledgeBaseTopic)
    {
        $keywords = $this->faker->words($this->faker->numberBetween(1, 7), true);
        $question = substr($this->faker->paragraph(1), 0, 60);

        $knowledgeBaseTopic->qas()->create([
            'question' => $question,
            'slug' => Str::slug($question),
            'answer' => $this->faker->paragraph(3),
            'keywords' => is_array($keywords) ? implode(', ', $keywords) : $keywords
        ]);
    }
}
