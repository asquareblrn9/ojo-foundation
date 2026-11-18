<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Single entry point for uploaded media (images, logos, avatars).
 *
 * Uses the disk named by MEDIA_DISK: "public" locally, "s3" in production.
 * Every stored file name is a fresh UUID, so files are immutable and can be
 * cached by browsers and CDNs for a year.
 */
class Media
{
    public static function diskName(): string
    {
        return config('foundation.media_disk', 'public');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(static::diskName());
    }

    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return preg_match('#^https?://#', $path) ? $path : static::disk()->url($path);
    }

    public static function put(string $path, string $contents): void
    {
        static::disk()->put($path, $contents, ['CacheControl' => 'public, max-age=31536000, immutable']);
    }
}
