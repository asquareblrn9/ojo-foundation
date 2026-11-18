<x-layouts.site :seo="$seo" transparent>
    <x-site.page-hero :title="$hero?->title ?? 'Our Programmes'" :subtitle="$hero?->subtitle" :eyebrow="$hero?->get('eyebrow')" :image="$hero?->image"
        :breadcrumbs="['Programmes' => null]" />

    <section class="bg-ivory py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($programmes->isEmpty())
                <x-site.empty-state icon="hand-heart" title="Programmes coming soon" text="Details of our programmes will be published here shortly." />
            @else
                <div class="space-y-8">
                    @foreach ($programmes as $i => $programme)
                        <article class="group grid overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-xl md:grid-cols-2" data-reveal>
                            <div @class(['relative aspect-[16/10] overflow-hidden md:aspect-auto md:min-h-[22rem]', 'md:order-2' => $i % 2 === 1])>
                                <x-site.img :path="$programme->featured_image" :alt="$programme->title" sizes="(min-width: 768px) 50vw, 100vw"
                                    class="absolute inset-0 size-full object-cover transition duration-700 group-hover:scale-105" />
                            </div>
                            <div class="flex flex-col justify-center p-8 sm:p-12">
                                <div class="flex items-center gap-3">
                                    @if ($programme->icon)
                                        <span class="grid size-11 place-items-center rounded-xl bg-navy-900 text-gold-300">{{ svg('lucide-'.$programme->icon, ['class' => 'size-5']) }}</span>
                                    @endif
                                    <span class="text-xs font-semibold tracking-[0.2em] text-gold-700 uppercase">Programme {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <h2 class="mt-5 font-serif text-3xl font-semibold text-navy-900">{{ $programme->title }}</h2>
                                <p class="mt-4 text-lg leading-relaxed text-slate-600">{{ $programme->short_description }}</p>
                                <div class="mt-8">
                                    <x-site.button :href="route('programmes.show', $programme)" variant="navy" icon="arrow-right">Learn more<span class="sr-only"> about {{ $programme->title }}</span></x-site.button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <x-site.cta-band />
</x-layouts.site>
