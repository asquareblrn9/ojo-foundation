<?php

namespace App\Livewire\Admin\Posts;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithToasts;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Index extends Component
{
    use RequiresPermission, WithPagination, WithToasts;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    /** all | published | draft | scheduled | featured | trashed */
    #[Url(except: 'all')]
    public string $status = 'all';

    #[Url(except: '')]
    public string $category = '';

    protected function permission(): Permission
    {
        return Permission::ManagePosts;
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'status', 'category'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function currentCategory(): ?Category
    {
        return $this->category ? $this->categories->firstWhere('slug', $this->category) : null;
    }

    #[Computed]
    public function counts(): array
    {
        $base = Post::query()->when($this->currentCategory, fn ($q, $c) => $q->where('category_id', $c->id));

        return [
            'all' => (clone $base)->count(),
            'published' => (clone $base)->published()->count(),
            'scheduled' => (clone $base)->where('status', PostStatus::Published)->where('published_at', '>', now())->count(),
            'draft' => (clone $base)->draft()->count(),
            'featured' => (clone $base)->where('is_featured', true)->count(),
            'trashed' => (clone $base)->onlyTrashed()->count(),
        ];
    }

    public function togglePublish(int $id, ActivityLogger $logger): void
    {
        $post = Post::findOrFail($id);

        if ($post->status === PostStatus::Published) {
            $post->update(['status' => PostStatus::Draft]);
            $logger->log('unpublished', "Unpublished post \"{$post->title}\"", $post);
            $this->toast('Post moved to drafts.');
        } else {
            $post->update(['status' => PostStatus::Published, 'published_at' => $post->published_at ?? now()]);
            $logger->log('published', "Published post \"{$post->title}\"", $post);
            $this->toast($post->published_at->isFuture() ? 'Post scheduled.' : 'Post published.');
        }
    }

    public function toggleFeatured(int $id): void
    {
        $post = Post::findOrFail($id);
        $post->update(['is_featured' => ! $post->is_featured]);
        $this->toast($post->is_featured ? 'Marked as featured.' : 'Removed from featured.');
    }

    public function delete(int $id): void
    {
        Post::findOrFail($id)->delete();
        $this->toast('Post moved to trash.');
    }

    public function restore(int $id): void
    {
        Post::onlyTrashed()->findOrFail($id)->restore();
        $this->toast('Post restored.');
    }

    public function forceDelete(int $id, ImageService $images): void
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $images->deleteWithVariants($post->featured_image);
        $post->tags()->detach();
        $post->forceDelete();
        $this->toast('Post permanently deleted.');
    }

    public function render()
    {
        $term = trim($this->search);

        $posts = Post::query()
            ->with(['category:id,name', 'author:id,name'])
            ->when($this->currentCategory, fn ($q, $c) => $q->where('category_id', $c->id))
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%")))
            ->when($this->status === 'published', fn ($q) => $q->published())
            ->when($this->status === 'draft', fn ($q) => $q->draft())
            ->when($this->status === 'scheduled', fn ($q) => $q->where('status', PostStatus::Published)->where('published_at', '>', now()))
            ->when($this->status === 'featured', fn ($q) => $q->where('is_featured', true))
            ->when($this->status === 'trashed', fn ($q) => $q->onlyTrashed())
            ->latest('updated_at')
            ->paginate(15);

        $isEvents = $this->currentCategory?->slug === 'events-activities';

        return view('livewire.admin.posts.index', ['posts' => $posts, 'isEvents' => $isEvents])
            ->title($isEvents ? 'Events & activities' : 'Blog posts');
    }
}
