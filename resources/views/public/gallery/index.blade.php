<x-layouts.site :seo="$seo" transparent>
    <x-site.page-hero title="Gallery" eyebrow="Moments that matter"
        subtitle="Photographs from our outreaches, classrooms and community activities."
        :breadcrumbs="['Gallery' => null]" />

    @if ($albums->isNotEmpty())
        <section class="py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading align="left" eyebrow="Albums" title="Events & activities" />
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($albums as $album)
                        @php $cover = $album->coverImage ?? $album->images->first(); @endphp
                        <a href="{{ route('gallery.album', $album) }}" class="group relative block aspect-[4/3] overflow-hidden rounded-3xl bg-navy-900" data-reveal>
                            @if ($cover)
                                <img src="{{ $cover->url('md') }}" srcset="{{ $cover->srcset() }}" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                                     alt="" loading="lazy" decoding="async" class="size-full object-cover transition duration-700 group-hover:scale-105">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-950/20 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-6">
                                <p class="text-xs font-semibold tracking-wide text-gold-300 uppercase">
                                    {{ $album->images_count }} {{ Str::plural('photo', $album->images_count) }}@if ($album->event_date) · {{ $album->event_date->format('M Y') }}@endif
                                </p>
                                <h3 class="mt-1 font-serif text-2xl font-semibold text-white">{{ $album->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section @class(['py-16 sm:py-20', 'bg-ivory' => $albums->isNotEmpty()])>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($albums->isNotEmpty())
                <x-site.section-heading align="left" eyebrow="All photos" title="Browse the gallery" class="mb-10" />
            @endif
            <livewire:public.gallery-grid />
        </div>
    </section>
</x-layouts.site>
