<?php

namespace App\Support;

/**
 * Builds src/srcset for images stored by ImageService::store(), whose
 * variants follow the "{name}-{size}.webp" naming convention.
 */
class ResponsiveImage
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : Media::disk()->url($path);
    }

    public static function srcset(?string $path): ?string
    {
        if (! $path || ! str_ends_with($path, '.webp') || str_starts_with($path, 'http')) {
            return null;
        }

        $base = substr($path, 0, -5);

        return collect(config('foundation.uploads.variant_widths'))
            ->map(fn (int $width, string $size) => Media::disk()->url("{$base}-{$size}.webp")." {$width}w")
            ->implode(', ');
    }

    /** URL of a single sized variant (sm|md|lg). */
    public static function variant(?string $path, string $size): ?string
    {
        if (! $path || ! str_ends_with($path, '.webp')) {
            return self::url($path);
        }

        return Media::disk()->url(substr($path, 0, -5)."-{$size}.webp");
    }
}
