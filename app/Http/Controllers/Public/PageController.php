<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Programme;
use App\Models\TeamMember;
use App\Support\Seo;
use Illuminate\View\View;

/** CMS-driven content pages: About, Get Involved, Contact and legal pages. */
class PageController extends Controller
{
    public function about(): View
    {
        $page = $this->page('about');

        return view('public.about', [
            'page' => $page,
            'sections' => $page->sectionMap(),
            'team' => TeamMember::active()->get(),
            'seo' => $this->seo($page),
        ]);
    }

    public function getInvolved(): View
    {
        $page = $this->page('get-involved');

        return view('public.get-involved', [
            'page' => $page,
            'sections' => $page->sectionMap(),
            'programmes' => Programme::published()->ordered()->pluck('title'),
            'seo' => $this->seo($page),
        ]);
    }

    public function contact(): View
    {
        $page = $this->page('contact');

        return view('public.contact', [
            'page' => $page,
            'sections' => $page->sectionMap(),
            'seo' => $this->seo($page),
        ]);
    }

    public function legal(string $slug): View
    {
        $page = $this->page($slug);

        return view('public.legal', ['page' => $page, 'seo' => $this->seo($page)]);
    }

    private function page(string $slug): Page
    {
        return Page::findCached($slug) ?? abort(404);
    }

    private function seo(Page $page): Seo
    {
        $hero = $page->sectionMap()->get('hero');

        return Seo::make($page->seo_title ?: $page->title, $page->seo_description ?: $hero?->subtitle, $hero?->image);
    }
}
