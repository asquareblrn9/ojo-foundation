<?php

namespace App\Models;

use App\Support\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

#[Fillable(['slug', 'title', 'body', 'seo_title', 'seo_description', 'is_published', 'is_system'])]
class Page extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted(): void
    {
        static::saved(fn (self $page) => static::forgetCache($page->slug));
        static::deleted(fn (self $page) => static::forgetCache($page->slug));
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    /** Active sections keyed by their key, for easy lookup in views. */
    public function sectionMap(): Collection
    {
        return $this->sections->where('is_active', true)->keyBy('key');
    }

    /**
     * Load a published page with its sections, cached until the page or
     * any of its sections is edited.
     *
     * Only raw attribute arrays are cached (the cache store is configured
     * not to unserialize PHP objects); models are rehydrated on read.
     */
    public static function findCached(string $slug): ?self
    {
        $cached = Cache::rememberForever(static::cacheKey($slug), function () use ($slug) {
            $page = static::query()->with('sections')->where('slug', $slug)->where('is_published', true)->first();

            return $page ? [
                'page' => $page->getAttributes(),
                'sections' => $page->sections->map(fn (PageSection $s) => $s->getAttributes())->all(),
            ] : ['page' => null];
        });

        if (empty($cached['page'])) {
            return null;
        }

        $page = (new static)->newFromBuilder($cached['page']);
        $page->setRelation('sections', (new PageSection)->newCollection(
            array_map(fn (array $attributes) => (new PageSection)->newFromBuilder($attributes), $cached['sections'])
        ));

        return $page;
    }

    public static function cacheKey(string $slug): string
    {
        return "page:{$slug}";
    }

    public static function forgetCache(string $slug): void
    {
        Cache::forget(static::cacheKey($slug));
    }
}
