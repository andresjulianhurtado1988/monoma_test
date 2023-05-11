<?php

namespace Database\Factories;
use App\Models\Candidate;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'source' => $this->faker->randomElement(['Fotocasa', 'Habitaclia']),
            'owner'  => $this->faker->randomElement([1,2]),
            'created_at' => now(),
            'created_by' => 1,
        ];
    }
}
