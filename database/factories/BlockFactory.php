<?php

namespace Database\Factories;

use App\Models\Block;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Block>
 */
class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Block::class;

    public function definition()
    {
        return [
            'is_active' => fake()->numberBetween(0, 1),
            'is_empty' => fake()->numberBetween(0, 1),
            'freezer_id' => fake()->numberBetween(1, 50),

        ];
    }
}
