<?php

use App\Services\SettingsService;
use Database\Seeders\BrandSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Support\Facades\Storage;

it('installs the official logo, favicon and sharing image', function () {
    Storage::fake('public');
    $this->seed([SettingsSeeder::class, BrandSeeder::class]);

    $settings = app(SettingsService::class);
    expect($settings->get('logo'))->toBe('brand/logo-emblem.png')
        ->and($settings->get('favicon'))->toBe('brand/logo-emblem.png')
        ->and($settings->get('og_image'))->toBe('brand/og-image.jpg');
    Storage::disk('public')->assertExists(['brand/logo-emblem.png', 'brand/og-image.jpg']);
});

it('never replaces a logo uploaded through the admin', function () {
    Storage::fake('public');
    $this->seed(SettingsSeeder::class);
    app(SettingsService::class)->set(['logo' => 'uploads/custom-logo.webp']);

    $this->seed(BrandSeeder::class);

    expect(app(SettingsService::class)->get('logo'))->toBe('uploads/custom-logo.webp');
});
