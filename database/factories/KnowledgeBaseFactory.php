<?php

namespace Database\Factories;

use App\Models\KnowledgeBaseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KnowledgeBase>
 */
class KnowledgeBaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $knowledgeBaseCategories = KnowledgeBaseCategory::pluck('id')->toArray();

        return [
            'knowledge_base_category_id' => fake()->randomElement($knowledgeBaseCategories),
            'question' => fake()->sentence(),
            'answer' => fake()->paragraph(),
        ];
    }
}
