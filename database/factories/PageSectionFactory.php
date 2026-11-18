<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageSectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'key' => fake()->unique()->slug(2),
            'type' => 'text',
            'title' => fake()->sentence(4),
            'content' => ['body' => fake()->paragraph()],
        ];
    }
}
