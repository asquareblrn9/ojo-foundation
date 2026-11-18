<?php

namespace Database\Factories;

use App\Models\GalleryCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryAlbumFactory extends Factory
{
    public function definition(): array
    {
        return [
            'gallery_category_id' => GalleryCategory::factory(),
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'description' => fake()->sentence(),
            'event_date' => fake()->dateTimeBetween('-1 year'),
            'is_published' => true,
        ];
    }
}
