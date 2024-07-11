<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KnowledgeBaseCategory>
 */
class KnowledgeBaseCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessIds = Business::pluck('id')->toArray(); // Adjust the number of businesses as needed
        $businessId = $this->faker->randomElement($businessIds);
        $name = substr(fake()->unique()->sentence(), 0, 60);
        // Get a user associated with the business
        $user = User::where('business_id', $businessId)->whereNotNull('business_id')->inRandomOrder()->first();

        if(!$user)
            $user = User::factory()->create(['business_id' => $businessId]);

        return [
            'business_id' => $businessId,
            'name' => $name,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ];
    }
}
