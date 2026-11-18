<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Support\Concerns\HasSlug;
use App\Support\Concerns\LogsActivity;
use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'featured_image',
    'featured_image_alt', 'status', 'is_featured', 'published_at', 'seo_title', 'seo_description',
])]
class Post extends Model
{
    use HasFactory, HasSlug, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** Published and with a publication date that has arrived (supports scheduling). */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): void
    {
        $query->where('status', PostStatus::Draft);
    }

    public function isLive(): bool
    {
        return $this->status === PostStatus::Published && $this->published_at?->isPast();
    }

    public function isScheduled(): bool
    {
        return $this->status === PostStatus::Published && $this->published_at?->isFuture();
    }

    public function featuredImageUrl(): ?string
    {
        return $this->featured_image ? Media::disk()->url($this->featured_image) : null;
    }

    public function summary(int $limit = 160): string
    {
        return $this->excerpt ?: Str::limit(trim(strip_tags((string) $this->body)), $limit);
    }

    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 200));
    }
}
