<?php

namespace App\Http\Controllers\Public;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Programme;
use App\Support\Seo;
use Illuminate\View\View;

class ProgrammeController extends Controller
{
    public function index(): View
    {
        $page = Page::findCached('programmes');
        $hero = $page?->sectionMap()->get('hero');

        return view('public.programmes.index', [
            'hero' => $hero,
            'programmes' => Programme::published()->ordered()->get(),
            'seo' => Seo::make($page?->seo_title ?: 'Our Programmes', $page?->seo_description ?: $hero?->subtitle, $hero?->image),
        ]);
    }

    public function show(Programme $programme): View
    {
        // Unpublished programmes are visible only to staff (preview).
        abort_unless($programme->is_published || auth()->user()?->can(Permission::ManageProgrammes->value), 404);

        $seo = Seo::make(
            $programme->seo_title ?: $programme->title,
            $programme->seo_description ?: $programme->short_description,
            $programme->featured_image,
        );
        $seo->noindex = ! $programme->is_published;

        return view('public.programmes.show', [
            'programme' => $programme,
            'others' => Programme::published()->ordered()->whereKeyNot($programme->id)->limit(3)->get(),
            'seo' => $seo,
        ]);
    }
}
