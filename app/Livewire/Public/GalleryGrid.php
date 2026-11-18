<?php

namespace App\Livewire\Public;

use App\Models\GalleryCategory;
use App\Models\GalleryImage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

/** Responsive photo grid with category filter, "load more" and a lightbox. */
class GalleryGrid extends Component
{
    private const PER_PAGE = 12;

    #[Url(except: '')]
    public string $category = '';

    /** Restrict to one album (album page). */
    #[Locked]
    public ?int $albumId = null;

    public int $limit = self::PER_PAGE;

    public function mount(?int $albumId = null): void
    {
        $this->albumId = $albumId;
    }

    public function filter(string $slug): void
    {
        $this->category = $slug;
        $this->limit = self::PER_PAGE;
    }

    public function loadMore(): void
    {
        $this->limit += self::PER_PAGE;
    }

    #[Computed]
    public function categories()
    {
        if ($this->albumId) {
            return collect();
        }

        return GalleryCategory::query()
            ->whereHas('images', fn ($q) => $q->publiclyVisible())
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);
    }

    public function render()
    {
        $query = GalleryImage::publiclyVisible()
            ->with('album:id,title,slug')
            ->when($this->albumId, fn ($q) => $q->where('gallery_album_id', $this->albumId))
            ->when($this->category !== '', fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
            ->orderByDesc('is_featured')->orderBy('sort_order')->latest('id');

        $total = (clone $query)->count();

        return view('livewire.public.gallery-grid', [
            'images' => $query->limit($this->limit)->get(),
            'hasMore' => $total > $this->limit,
        ]);
    }
}
