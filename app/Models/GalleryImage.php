<?php

namespace App\Models;

use App\Support\Concerns\LogsActivity;
use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'gallery_album_id', 'gallery_category_id', 'path', 'variants', 'caption', 'description',
    'alt', 'width', 'height', 'is_featured', 'is_visible', 'sort_order',
])]
class GalleryImage extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'variants' => 'array',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_visible', true);
    }

    /** Visible, and either not in an album or in a published album. */
    public function scopePubliclyVisible(Builder $query): void
    {
        $query->visible()->where(fn (Builder $q) => $q
            ->whereNull('gallery_album_id')
            ->orWhereHas('album', fn (Builder $a) => $a->where('is_published', true)));
    }

    /** URL of a named variant (sm|md|lg), falling back to the original. */
    public function url(string $size = 'lg'): string
    {
        $path = $this->variants[$size] ?? $this->path;

        return Media::disk()->url($path);
    }

    public function srcset(): string
    {
        return collect(['sm' => 400, 'md' => 800, 'lg' => 1600])
            ->filter(fn ($w, $size) => isset($this->variants[$size]))
            ->map(fn ($w, $size) => $this->url($size)." {$w}w")
            ->implode(', ');
    }

    public function activityName(): string
    {
        return $this->caption ?: basename($this->path);
    }
}
