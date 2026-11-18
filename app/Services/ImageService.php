<?php

namespace App\Services;

use App\Support\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Stores uploaded images on the media disk (local "public" or S3) as
 * optimised WebP files and generates responsive width variants.
 * Re-encoding every upload strips EXIF metadata (e.g. GPS location) and
 * neutralises any payload hidden in the original file.
 */
class ImageService
{
    private const MAX_ORIGINAL_WIDTH = 2400;

    private const QUALITY = 82;

    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = ImageManager::usingDriver(GdDriver::class);
    }

    /**
     * Store an image with responsive variants. Variants follow the naming
     * convention "{name}-{size}.webp", which ResponsiveImage relies on.
     *
     * @param  UploadedFile|string  $source  An upload or an absolute local file path.
     * @return array{path: string, variants: array<string,string>, width: int, height: int}
     */
    public function store(UploadedFile|string $source, string $directory, bool $withVariants = true): array
    {
        $sourcePath = $source instanceof UploadedFile ? $source->getRealPath() : $source;
        $base = $this->basePath($directory);

        $image = $this->decode($sourcePath)->scaleDown(width: self::MAX_ORIGINAL_WIDTH);
        $path = "{$base}.webp";
        Media::put($path, $this->encode($image));

        $result = ['path' => $path, 'variants' => [], 'width' => $image->width(), 'height' => $image->height()];

        if ($withVariants) {
            foreach (config('foundation.uploads.variant_widths') as $size => $width) {
                $variantPath = "{$base}-{$size}.webp";
                Media::put($variantPath, $this->encode($this->decode($sourcePath)->scaleDown(width: $width)));
                $result['variants'][$size] = $variantPath;
            }
        }

        return $result;
    }

    /** Store a single optimised image (no variants) and return its path. */
    public function storeSingle(UploadedFile|string $source, string $directory, int $maxWidth = 1600): string
    {
        $sourcePath = $source instanceof UploadedFile ? $source->getRealPath() : $source;
        $path = $this->basePath($directory).'.webp';

        Media::put($path, $this->encode($this->decode($sourcePath)->scaleDown(width: $maxWidth)));

        return $path;
    }

    /**
     * Store a file without re-encoding (e.g. an .ico favicon). Callers must
     * validate the MIME type first; SVG must never be accepted here.
     * The extension is derived from the detected MIME type, not the client name.
     */
    public function storeRaw(UploadedFile $file, string $directory): string
    {
        $path = $this->basePath($directory).'.'.($file->extension() ?: 'bin');
        Media::put($path, (string) file_get_contents($file->getRealPath()));

        return $path;
    }

    /** Delete an image stored by store(), including its conventional variants. */
    public function deleteWithVariants(?string $path): void
    {
        if (! $path) {
            return;
        }

        $base = preg_replace('/\.webp$/', '', $path);
        $this->delete([$path, ...array_map(fn ($size) => "{$base}-{$size}.webp", array_keys(config('foundation.uploads.variant_widths')))]);
    }

    /** @param array<int, string|null>|string|null $paths */
    public function delete(array|string|null $paths): void
    {
        $paths = array_values(array_filter((array) $paths, fn ($p) => $p && ! preg_match('#^https?://#', $p)));

        if ($paths) {
            Media::disk()->delete($paths);
        }
    }

    private function basePath(string $directory): string
    {
        return trim($directory, '/').'/'.now()->format('Y/m').'/'.Str::uuid();
    }

    private function decode(string $path): ImageInterface
    {
        return $this->manager->decodePath($path)->orient();
    }

    private function encode(ImageInterface $image): string
    {
        return $image->encode(new WebpEncoder(quality: self::QUALITY, strip: true))->toString();
    }
}
