<?php

namespace Database\Factories;

use App\Models\Freezer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FreezerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    protected $model = Freezer::class;

    public function definition()
    {
        return [
            'temperature' => fake()->numberBetween(-20, 50),
            'is_active' => fake()->numberBetween(0, 1),
            'location_id' => fake()->numberBetween(1, 6),
        ];
    }
}
