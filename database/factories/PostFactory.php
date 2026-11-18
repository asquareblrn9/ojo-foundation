<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'author_id' => User::factory(),
            'title' => rtrim(fake()->sentence(6), '.'),
            'excerpt' => fake()->sentence(18),
            'body' => '<p>'.implode('</p><p>', fake()->paragraphs(4)).'</p>',
            'status' => PostStatus::Draft,
            'is_featured' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => PostStatus::Published, 'published_at' => now()->subDay()]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['status' => PostStatus::Published, 'published_at' => now()->addWeek()]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
