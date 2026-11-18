<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Database\Seeder;

/** Inserts any missing settings with their defaults; never overwrites edited values. */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('foundation.settings') as $key => $definition) {
            Setting::firstOrCreate(['key' => $key], [
                'group' => $definition['group'],
                'type' => $definition['type'],
                'value' => match (true) {
                    $definition['type'] === 'bool' => $definition['default'] ? '1' : '0',
                    is_array($definition['default']) => json_encode($definition['default']),
                    default => $definition['default'],
                },
            ]);
        }

        app(SettingsService::class)->flush();
    }
}
