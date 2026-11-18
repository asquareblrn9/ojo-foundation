<?php

namespace App\Models;

use App\Support\Concerns\HasSlug;
use App\Support\Concerns\LogsActivity;
use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'title', 'slug', 'icon', 'featured_image', 'short_description', 'description', 'objectives',
    'activities', 'outcomes', 'is_published', 'is_featured', 'sort_order', 'seo_title', 'seo_description',
])]
class Programme extends Model
{
    use HasFactory, HasSlug, LogsActivity, SoftDeletes;

    public const FOOTER_CACHE_KEY = 'footer_programmes';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::FOOTER_CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::FOOTER_CACHE_KEY));
    }

    protected function casts(): array
    {
        return [
            'objectives' => 'array',
            'activities' => 'array',
            'outcomes' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('title');
    }

    public function featuredImageUrl(): ?string
    {
        return $this->featured_image ? Media::disk()->url($this->featured_image) : null;
    }
}
