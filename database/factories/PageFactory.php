<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'title' => ucfirst(fake()->words(2, true)),
            'body' => '<p>'.fake()->paragraph().'</p>',
            'is_published' => true,
        ];
    }
}
