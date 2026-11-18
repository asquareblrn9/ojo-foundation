<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\Page;
use App\Models\Post;
use App\Models\Programme;
use App\Support\Seo;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $page = Page::findCached('home');
        $sections = $page?->sectionMap() ?? collect();

        $postColumns = ['id', 'category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'featured_image', 'featured_image_alt', 'status', 'published_at', 'is_featured'];

        return view('public.home', [
            'sections' => $sections,
            'programmes' => Programme::published()->ordered()->limit(6)
                ->get(['id', 'title', 'slug', 'icon', 'featured_image', 'short_description']),
            'featuredPosts' => Post::published()->with('category:id,name,slug')
                ->orderByDesc('is_featured')->latest('published_at')->limit(3)->get($postColumns),
            'events' => Post::published()->with('category:id,name,slug')
                ->whereHas('category', fn ($q) => $q->where('slug', 'events-activities'))
                ->latest('published_at')->limit(3)->get($postColumns),
            'galleryImages' => GalleryImage::publiclyVisible()
                ->orderByDesc('is_featured')->orderBy('sort_order')->latest()->limit(8)->get(),
            'seo' => Seo::make($page?->seo_title, $page?->seo_description, $sections->get('hero')?->image),
        ]);
    }
}
