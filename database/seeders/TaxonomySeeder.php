<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GalleryCategory;
use Illuminate\Database\Seeder;

/** Starter blog and gallery categories. All are editable in the admin. */
class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $blogCategories = [
            ['name' => 'News', 'description' => 'Announcements and updates from the foundation.'],
            ['name' => 'Events & Activities', 'description' => 'Outreaches, events and activities carried out by the foundation.'],
            ['name' => 'Stories of Impact', 'description' => 'Stories from the people and communities we serve.'],
            ['name' => 'Education', 'description' => null],
            ['name' => 'Health', 'description' => null],
            ['name' => 'Empowerment', 'description' => null],
        ];

        foreach ($blogCategories as $i => $category) {
            Category::firstOrCreate(['name' => $category['name']], $category + ['sort_order' => $i]);
        }

        $galleryCategories = ['Outreaches', 'Education', 'Healthcare', 'Community Development', 'Women & Youth Empowerment', 'Events'];

        foreach ($galleryCategories as $i => $name) {
            GalleryCategory::firstOrCreate(['name' => $name], ['sort_order' => $i]);
        }
    }
}
