<?php

namespace App\Support\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Generates a unique slug from the model's slug source column when
 * the slug is empty. Models may override slugSource().
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            $source = $model->getAttribute($model->slugSource());

            if (blank($model->slug) && filled($source)) {
                $model->slug = $source;
            }

            if ($model->isDirty('slug') && filled($model->slug)) {
                $model->slug = static::uniqueSlug(Str::slug($model->slug), $model->getKey());
            }
        });
    }

    public function slugSource(): string
    {
        return 'title';
    }

    public static function uniqueSlug(string $base, mixed $ignoreId = null): string
    {
        $base = $base !== '' ? Str::limit($base, 180, '') : Str::lower(Str::random(8));
        $slug = $base;
        $i = 2;

        while (static::slugExists($slug, $ignoreId)) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    protected static function slugExists(string $slug, mixed $ignoreId): bool
    {
        $query = in_array(SoftDeletes::class, class_uses_recursive(static::class), true)
            ? static::withTrashed()
            : static::query();

        return $query->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
