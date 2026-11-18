<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Support\Seo;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('public.gallery.index', [
            'albums' => GalleryAlbum::published()
                ->whereHas('images', fn ($q) => $q->visible())
                ->with(['coverImage', 'images' => fn ($q) => $q->visible()->limit(1)])
                ->withCount(['images' => fn ($q) => $q->visible()])
                ->orderBy('sort_order')->latest('event_date')
                ->get(),
            'seo' => Seo::make('Gallery', 'Photographs from the work and activities of '.setting('site_name').'.'),
        ]);
    }

    public function album(GalleryAlbum $album): View
    {
        abort_unless($album->is_published, 404);

        return view('public.gallery.album', [
            'album' => $album,
            'seo' => Seo::make($album->title, $album->description, $album->coverImage?->path),
        ]);
    }
}
