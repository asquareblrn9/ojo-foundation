<?php

namespace App\Models;

use App\Support\Concerns\HasSlug;
use App\Support\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['gallery_category_id', 'title', 'slug', 'description', 'cover_image_id', 'event_date', 'is_published', 'sort_order'])]
class GalleryAlbum extends Model
{
    use HasFactory, HasSlug, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('sort_order');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(GalleryImage::class, 'cover_image_id');
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }
}
