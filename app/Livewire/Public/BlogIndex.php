<?php

namespace App\Livewire\Public;

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/** Blog listing with search and category filtering, synced to the URL. */
class BlogIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    /** Fixed tag filter when rendered on a tag archive page. */
    #[Locked]
    public ?int $tagId = null;

    public function mount(?string $initialCategory = null): void
    {
        $this->category = $initialCategory ?? $this->category;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function filterCategory(string $slug): void
    {
        $this->category = $this->category === $slug ? '' : $slug;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'category');
        $this->resetPage();
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function featured(): ?Post
    {
        if ($this->search !== '' || $this->category !== '' || $this->tagId || $this->getPage() > 1) {
            return null;
        }

        return Post::published()->where('is_featured', true)
            ->with(['category:id,name,slug', 'author:id,name'])
            ->latest('published_at')->first();
    }

    public function render()
    {
        $term = trim(mb_substr($this->search, 0, 100));

        $posts = Post::published()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->when($this->featured, fn ($q) => $q->whereKeyNot($this->featured->id))
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('body', 'like', "%{$term}%")))
            ->when($this->category !== '', fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
            ->when($this->tagId, fn ($q) => $q->whereHas('tags', fn ($t) => $t->whereKey($this->tagId)))
            ->latest('published_at')
            ->paginate(9);

        return view('livewire.public.blog-index', ['posts' => $posts]);
    }
}
