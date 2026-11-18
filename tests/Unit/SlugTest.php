<?php

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('generates unique slugs from titles', function () {
    $first = Post::factory()->create(['title' => 'Back to School Outreach']);
    $second = Post::factory()->create(['title' => 'Back to School Outreach']);

    expect($first->slug)->toBe('back-to-school-outreach')
        ->and($second->slug)->toBe('back-to-school-outreach-2');
});

it('only treats published posts with a past date as live', function () {
    Post::factory()->create();
    Post::factory()->published()->create();
    Post::factory()->scheduled()->create();

    expect(Post::published()->count())->toBe(1);
});
