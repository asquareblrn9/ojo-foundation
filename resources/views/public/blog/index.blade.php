<x-layouts.site :seo="$seo" transparent>
    @php
        $title = $category?->name ?? ($tag ? "Tagged “{$tag->name}”" : 'News & Stories');
        $crumbs = ['News' => ($category || $tag) ? route('blog.index') : null];
        if ($category || $tag) {
            $crumbs[$category?->name ?? $tag->name] = null;
        }
    @endphp

    <x-site.page-hero :title="$title" eyebrow="News & stories"
        :subtitle="$category?->description ?? 'News, events and stories of impact from '.setting('site_name').'.'"
        :breadcrumbs="$crumbs" />

    <section class="bg-ivory py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:public.blog-index :initial-category="$category?->slug" :tag-id="$tag?->id" />
        </div>
    </section>

    <x-site.newsletter class="!bg-white" />
</x-layouts.site>
