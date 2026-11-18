<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryImageFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->uuid();

        return [
            'path' => "gallery/{$name}.webp",
            'variants' => ['sm' => "gallery/{$name}-sm.webp", 'md' => "gallery/{$name}-md.webp", 'lg' => "gallery/{$name}-lg.webp"],
            'caption' => fake()->sentence(4),
            'alt' => fake()->sentence(6),
            'width' => 1600,
            'height' => 1067,
            'is_visible' => true,
        ];
    }
}
