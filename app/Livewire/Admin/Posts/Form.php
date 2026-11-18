<?php

namespace App\Livewire\Admin\Posts;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ActivityLogger;
use App\Services\ImageService;
use App\Support\Concerns\HandlesImageUpload;
use App\Support\Concerns\RequiresPermission;
use App\Support\Concerns\WithEditorUploads;
use App\Support\Concerns\WithToasts;
use App\Support\ResponsiveImage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Stevebauman\Purify\Facades\Purify;

#[Layout('layouts.admin')]
class Form extends Component
{
    use HandlesImageUpload, RequiresPermission, WithEditorUploads, WithFileUploads, WithToasts;

    #[Locked]
    public ?int $postId = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $body = '';

    public ?int $category_id = null;

    public string $tags = '';

    public bool $is_featured = false;

    /** Local date-time string (Y-m-d\TH:i) for the datetime-local input. */
    public string $published_at = '';

    public string $seo_title = '';

    public string $seo_description = '';

    public string $featured_image_alt = '';

    public $image;

    #[Locked]
    public ?string $currentImage = null;

    public bool $removeImage = false;

    #[Locked]
    public string $currentStatus = 'draft';

    protected function permission(): Permission
    {
        return Permission::ManagePosts;
    }

    public function mount(?Post $post = null): void
    {
        if ($post?->exists) {
            abort_if($post->trashed(), 404);

            $this->postId = $post->id;
            $this->fill($post->only(['title', 'slug', 'category_id', 'is_featured']));
            $this->excerpt = (string) $post->excerpt;
            $this->body = (string) $post->body;
            $this->seo_title = (string) $post->seo_title;
            $this->seo_description = (string) $post->seo_description;
            $this->featured_image_alt = (string) $post->featured_image_alt;
            $this->tags = $post->tags->pluck('name')->implode(', ');
            $this->published_at = $post->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->currentImage = $post->featured_image;
            $this->currentStatus = $post->status->value;

            return;
        }

        if ($slug = request()->query('category')) {
            $this->category_id = Category::where('slug', $slug)->value('id');
        }
    }

    #[Computed]
    public function categories(): array
    {
        return Category::orderBy('sort_order')->orderBy('name')->pluck('name', 'id')->all();
    }

    #[Computed]
    public function post(): ?Post
    {
        return $this->postId ? Post::find($this->postId) : null;
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->postId && $this->slug === '') {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedImage(): void
    {
        $this->validateOnly('image', ['image' => $this->imageRule()]);
        $this->removeImage = false;
    }

    public function clearImage(): void
    {
        $this->reset('image');
        $this->removeImage = true;
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:190', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($this->postId)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:200000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'image' => $this->imageRule(),
        ];
    }

    protected function validationAttributes(): array
    {
        return ['category_id' => 'category', 'image' => 'featured image', 'seo_title' => 'SEO title', 'seo_description' => 'SEO description'];
    }

    public function saveDraft(ImageService $images, ActivityLogger $logger): void
    {
        $this->persist(PostStatus::Draft, $images, $logger);
    }

    public function publish(ImageService $images, ActivityLogger $logger): void
    {
        $this->persist(PostStatus::Published, $images, $logger);
    }

    /** Save without changing the publication status. */
    public function save(ImageService $images, ActivityLogger $logger): void
    {
        $this->persist(PostStatus::from($this->currentStatus), $images, $logger);
    }

    private function persist(PostStatus $status, ImageService $images, ActivityLogger $logger): void
    {
        $this->validate();

        $post = $this->postId ? Post::findOrFail($this->postId) : new Post(['author_id' => auth()->id()]);
        $wasPublished = $post->exists && $post->status === PostStatus::Published;

        $publishedAt = $this->published_at !== '' ? Carbon::parse($this->published_at) : null;
        if ($status === PostStatus::Published && ! $publishedAt) {
            $publishedAt = now();
        }

        $post->fill([
            'title' => $this->title,
            'slug' => $this->slug !== '' ? $this->slug : null,
            'excerpt' => $this->excerpt ?: null,
            'body' => $this->body !== '' ? Purify::clean($this->body) : null,
            'category_id' => $this->category_id ?: null,
            'is_featured' => $this->is_featured,
            'status' => $status,
            'published_at' => $publishedAt,
            'seo_title' => $this->seo_title ?: null,
            'seo_description' => $this->seo_description ?: null,
            'featured_image_alt' => $this->featured_image_alt ?: null,
        ]);

        if ($this->image) {
            $images->deleteWithVariants($post->featured_image);
            $post->featured_image = $images->store($this->image, 'posts')['path'];
        } elseif ($this->removeImage && $post->featured_image) {
            $images->deleteWithVariants($post->featured_image);
            $post->featured_image = null;
        }

        $post->save();
        $post->tags()->sync($this->resolveTagIds());

        if ($status === PostStatus::Published && ! $wasPublished) {
            $logger->log('published', "Published post \"{$post->title}\"", $post);
        } elseif ($status === PostStatus::Draft && $wasPublished) {
            $logger->log('unpublished', "Unpublished post \"{$post->title}\"", $post);
        }

        $message = match (true) {
            $status === PostStatus::Published && $post->published_at->isFuture() => 'Post scheduled for '.$post->published_at->format('j M Y, g:ia').'.',
            $status === PostStatus::Published => 'Post published.',
            default => 'Draft saved.',
        };

        if (! $this->postId) {
            $this->flashToast($message);
            $this->redirectRoute('admin.posts.edit', ['post' => $post->id], navigate: true);

            return;
        }

        $this->reset('image', 'removeImage');
        $this->currentImage = $post->featured_image;
        $this->currentStatus = $post->status->value;
        $this->slug = $post->slug;
        $this->published_at = $post->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->body = (string) $post->body;
        unset($this->post);
        $this->toast($message);
    }

    /** @return list<int> */
    private function resolveTagIds(): array
    {
        return collect(explode(',', $this->tags))
            ->map(fn ($t) => trim(strip_tags($t)))
            ->filter(fn ($t) => $t !== '' && mb_strlen($t) <= 50)
            ->unique(fn ($t) => Str::slug($t))
            ->map(fn ($name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id)
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.admin.posts.form', [
            'currentImageUrl' => $this->removeImage ? null : ResponsiveImage::variant($this->currentImage, 'md'),
        ])->title($this->postId ? 'Edit post' : 'New post');
    }
}
