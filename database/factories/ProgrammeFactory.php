<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProgrammeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'short_description' => fake()->sentence(16),
            'description' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
            'objectives' => fake()->sentences(3),
            'activities' => fake()->sentences(3),
            'outcomes' => fake()->sentences(2),
            'is_published' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['is_published' => true]);
    }
}
