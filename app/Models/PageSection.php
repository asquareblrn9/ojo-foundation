<?php

namespace App\Models;

use App\Support\Concerns\LogsActivity;
use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

#[Fillable(['page_id', 'key', 'type', 'title', 'subtitle', 'content', 'image', 'sort_order', 'is_active'])]
class PageSection extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted(): void
    {
        $flush = function (self $section) {
            if ($slug = Page::whereKey($section->page_id)->value('slug')) {
                Page::forgetCache($slug);
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /** Read a value from the JSON content using dot notation. */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->content ?? [], $key, $default);
    }

    public function items(): array
    {
        return $this->get('items', []);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Media::disk()->url($this->image) : null;
    }

    public function activityName(): string
    {
        return $this->title ?: $this->key;
    }
}
