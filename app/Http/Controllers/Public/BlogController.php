<?php

namespace App\Http\Controllers\Public;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Support\Seo;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        return view('public.blog.index', [
            'category' => null,
            'tag' => null,
            'seo' => Seo::make('News & Stories', 'News, events and stories of impact from '.setting('site_name').'.'),
        ]);
    }

    public function category(Category $category): View
    {
        return view('public.blog.index', [
            'category' => $category,
            'tag' => null,
            'seo' => Seo::make($category->name, $category->description ?: "Articles in {$category->name}."),
        ]);
    }

    public function tag(Tag $tag): View
    {
        return view('public.blog.index', [
            'category' => null,
            'tag' => $tag,
            'seo' => Seo::make("Tagged: {$tag->name}", "Articles tagged {$tag->name}."),
        ]);
    }

    public function show(Post $post): View
    {
        $isPreview = ! $post->isLive();

        // Drafts and scheduled posts are visible only to staff (preview).
        abort_if($isPreview && ! auth()->user()?->can(Permission::ManagePosts->value), 404);

        $post->load(['category:id,name,slug', 'author:id,name,job_title,avatar', 'tags:id,name,slug']);

        if (! $isPreview) {
            Post::whereKey($post->id)->increment('views');
        }

        $seo = Seo::make($post->seo_title ?: $post->title, $post->seo_description ?: $post->summary(), $post->featured_image)
            ->article($post->published_at?->toIso8601String(), $post->updated_at?->toIso8601String())
            ->schema(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'description' => $post->summary(),
                'image' => $post->featuredImageUrl(),
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
                'author' => $post->author ? ['@type' => 'Person', 'name' => $post->author->name] : null,
                'publisher' => ['@type' => 'Organization', 'name' => setting('site_name')],
                'mainEntityOfPage' => route('blog.show', $post),
            ]));
        $seo->noindex = $isPreview;

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->where(fn ($q) => $q->where('category_id', $post->category_id)
                ->orWhereHas('tags', fn ($t) => $t->whereIn('tags.id', $post->tags->pluck('id'))))
            ->with('category:id,name,slug')
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.blog.show', compact('post', 'related', 'seo', 'isPreview'));
    }
}
