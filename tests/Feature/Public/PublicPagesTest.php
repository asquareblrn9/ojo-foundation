<?php

use App\Enums\Role;
use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Post;
use App\Models\Programme;
use App\Services\SettingsService;
use Database\Seeders\SettingsSeeder;

beforeEach(function () {
    $this->seed(SettingsSeeder::class);

    foreach (['home', 'about', 'programmes', 'get-involved', 'contact', 'privacy-policy', 'terms'] as $slug) {
        $page = Page::factory()->create(['slug' => $slug, 'title' => ucfirst($slug).' page']);
        PageSection::factory()->for($page)->create(['key' => 'hero', 'type' => 'hero', 'title' => "Hero for {$slug}"]);
    }
});

it('renders every public page', function (string $route, string $expected) {
    $this->get(route($route))->assertOk()->assertSee($expected, false);
})->with([
    'home' => ['home', 'Hero for home'],
    'about' => ['about', 'Hero for about'],
    'programmes' => ['programmes.index', 'Hero for programmes'],
    'blog' => ['blog.index', 'News &amp; Stories'],
    'gallery' => ['gallery.index', 'Gallery'],
    'get involved' => ['get-involved', 'Hero for get-involved'],
    'contact' => ['contact', 'Hero for contact'],
    'privacy' => ['privacy', 'Privacy-policy page'],
    'terms' => ['terms', 'Terms page'],
]);

it('outputs SEO metadata', function () {
    $this->get(route('about'))
        ->assertSee('<link rel="canonical" href="'.route('about').'">', false)
        ->assertSee('property="og:title"', false)
        ->assertSee('name="twitter:card"', false)
        ->assertSee('application/ld+json', false);
});

it('returns a branded 404 for unknown pages', function () {
    $this->get('/this-page-does-not-exist')->assertNotFound()->assertSee("We couldn't find that page", false);
});

it('hides inactive page sections', function () {
    PageSection::where('key', 'hero')->whereHas('page', fn ($q) => $q->where('slug', 'about'))->update(['is_active' => false]);
    Page::forgetCache('about');

    $this->get(route('about'))->assertOk()->assertDontSee('Hero for about');
});

it('refreshes cached page content when a section is edited', function () {
    $this->get(route('about'))->assertSee('Hero for about');

    PageSection::where('key', 'hero')->whereHas('page', fn ($q) => $q->where('slug', 'about'))->first()
        ->update(['title' => 'Updated hero title']);

    $this->get(route('about'))->assertSee('Updated hero title')->assertDontSee('Hero for about');
});

it('lists only published programmes', function () {
    Programme::factory()->published()->create(['title' => 'Visible Programme']);
    Programme::factory()->create(['title' => 'Hidden Programme']);

    $this->get(route('programmes.index'))->assertSee('Visible Programme')->assertDontSee('Hidden Programme');
});

it('hides unpublished programmes from guests but lets staff preview them', function () {
    $programme = Programme::factory()->create(['title' => 'Draft Programme']);

    $this->get(route('programmes.show', $programme))->assertNotFound();

    actingAsRole(Role::ContentEditor);
    $this->get(route('programmes.show', $programme))->assertOk()->assertSee('Preview');
});

it('shows published posts and hides drafts and scheduled posts from the public', function () {
    $live = Post::factory()->published()->create(['title' => 'Live Article']);
    $draft = Post::factory()->create(['title' => 'Draft Article']);
    $scheduled = Post::factory()->scheduled()->create(['title' => 'Future Article']);

    $this->get(route('blog.index'))->assertSee('Live Article')->assertDontSee('Draft Article')->assertDontSee('Future Article');
    $this->get(route('blog.show', $live))->assertOk()->assertSee('Live Article');
    $this->get(route('blog.show', $draft))->assertNotFound();
    $this->get(route('blog.show', $scheduled))->assertNotFound();

    expect($live->fresh()->views)->toBe(1);
});

it('lets staff preview a draft post without counting a view', function () {
    $draft = Post::factory()->create(['title' => 'Draft Article']);
    actingAsRole(Role::ContentEditor);

    $this->get(route('blog.show', $draft))->assertOk()->assertSee('Preview')->assertSee('noindex', false);
    expect($draft->fresh()->views)->toBe(0);
});

it('filters blog posts by category archive', function () {
    $news = Post::factory()->published()->create(['title' => 'News Item']);
    Post::factory()->published()->create(['title' => 'Other Item']);

    $this->get(route('blog.category', $news->category))->assertSee('News Item')->assertDontSee('Other Item');
});

it('only shows visible images in published albums', function () {
    $published = GalleryAlbum::factory()->create(['is_published' => true]);
    $hidden = GalleryAlbum::factory()->create(['is_published' => false]);

    GalleryImage::factory()->for($published, 'album')->create(['caption' => 'Shown photo']);
    GalleryImage::factory()->for($published, 'album')->create(['caption' => 'Invisible photo', 'is_visible' => false]);
    GalleryImage::factory()->for($hidden, 'album')->create(['caption' => 'Unpublished album photo']);

    $this->get(route('gallery.index'))
        ->assertSee('Shown photo')
        ->assertDontSee('Invisible photo')
        ->assertDontSee('Unpublished album photo');

    $this->get(route('gallery.album', $hidden))->assertNotFound();
});

it('shows the donation call to action only when enabled', function () {
    $this->get(route('home'))->assertDontSee('Donate now');

    app(SettingsService::class)->set(['donations_enabled' => true, 'donation_url' => 'https://example.org/give']);

    $this->get(route('home'))->assertSee('Donate now')->assertSee('https://example.org/give');
});
