<x-layouts.site :seo="$seo" transparent>
    <x-site.page-hero :title="$album->title" :subtitle="$album->description"
        :eyebrow="$album->event_date ? $album->event_date->format('j F Y') : 'Album'"
        :image="$album->coverImage?->path"
        :breadcrumbs="['Gallery' => route('gallery.index'), $album->title => null]" />

    <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:public.gallery-grid :album-id="$album->id" />
        </div>
    </section>
</x-layouts.site>
