<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use App\Support\Media;
use Illuminate\Database\Seeder;

/**
 * Installs the official Fola Ojo Foundation logo assets
 * (database/seeders/images/brand) as the site logo, favicon and default
 * social-sharing image. Only fills settings that are still empty, so
 * logos uploaded through the admin are never replaced.
 */
class BrandSeeder extends Seeder
{
    public function run(SettingsService $settings): void
    {
        $assets = [
            'logo' => 'logo-emblem.png',
            'favicon' => 'logo-emblem.png',
            'og_image' => 'og-image.jpg',
        ];

        $updates = [];

        foreach ($assets as $key => $file) {
            if (filled($settings->get($key))) {
                continue;
            }

            $target = "brand/{$file}";

            if (! Media::disk()->exists($target)) {
                Media::disk()->put($target, file_get_contents(database_path("seeders/images/brand/{$file}")));
            }

            $updates[$key] = $target;
        }

        if ($updates) {
            $settings->set($updates);
        }
    }
}
