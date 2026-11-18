<x-layouts.site :seo="$seo" transparent>
    <x-site.page-hero :title="$programme->title" :subtitle="$programme->short_description" eyebrow="Programme" :image="$programme->featured_image"
        :breadcrumbs="['Programmes' => route('programmes.index'), $programme->title => null]" />

    @unless ($programme->is_published)
        <div class="bg-amber-100 px-4 py-3 text-center text-sm font-medium text-amber-900">Preview — this programme is not published.</div>
    @endunless

    <section class="py-20 sm:py-24">
        <div class="mx-auto grid max-w-7xl gap-14 px-4 sm:px-6 lg:grid-cols-12 lg:px-8">
            <div class="lg:col-span-8">
                @if ($programme->description)
                    {{-- Rich text is sanitised with HTMLPurifier when saved. --}}
                    <div class="prose-foundation" data-reveal>{!! $programme->description !!}</div>
                @endif

                @foreach ([['objectives', 'Objectives', 'target'], ['activities', 'Activities', 'list-checks'], ['outcomes', 'Impact & outcomes', 'trending-up']] as [$field, $label, $icon])
                    @if (! empty($programme->{$field}))
                        <div class="mt-12" data-reveal>
                            <h2 class="flex items-center gap-3 font-serif text-2xl font-semibold text-navy-900">
                                <span class="grid size-10 place-items-center rounded-xl bg-gold-100 text-gold-800">{{ svg('lucide-'.$icon, ['class' => 'size-5']) }}</span>
                                {{ $label }}
                            </h2>
                            <ul class="mt-6 space-y-3">
                                @foreach ($programme->{$field} as $point)
                                    <li class="flex gap-3 text-lg text-slate-700">
                                        <x-lucide-circle-check class="mt-1 size-5 shrink-0 text-gold-600" />
                                        <span>{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>

            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    <div class="rounded-3xl bg-navy-900 p-8 text-white">
                        <h2 class="font-serif text-2xl font-semibold">Support this programme</h2>
                        <p class="mt-3 text-navy-100">Sponsor a child, contribute materials or volunteer your expertise to help this programme reach more young people.</p>
                        <div class="mt-6 flex flex-col gap-3">
                            <x-site.button :href="route('get-involved')" icon="arrow-right">Get involved</x-site.button>
                            <x-site.button :href="route('contact')" variant="outline-light">Contact us</x-site.button>
                        </div>
                    </div>
                    <x-site.share :url="route('programmes.show', $programme)" :title="$programme->title" />
                </div>
            </aside>
        </div>
    </section>

    @if ($others->isNotEmpty())
        <section class="bg-ivory py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading eyebrow="Explore more" title="Other programmes" />
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($others as $other)
                        <x-site.programme-card :programme="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>
