@php
    $hero = $sections->get('hero');
    $intro = $sections->get('intro');
    $mv = $sections->get('mission_vision');
    $focus = $sections->get('focus');
    $programmeSection = $sections->get('programmes');
    $stats = $sections->get('stats');
    $quote = $sections->get('quote');
    $news = $sections->get('news');
    $eventsSection = $sections->get('events');
    $gallery = $sections->get('gallery');
    $involve = $sections->get('involve');
    $preload = $hero?->image ? ['src' => \App\Support\ResponsiveImage::variant($hero->image, 'md'), 'srcset' => \App\Support\ResponsiveImage::srcset($hero->image)] : null;
@endphp

<x-layouts.site :seo="$seo" transparent :preload="$preload">

    {{-- 1. Hero --}}
    @if ($hero)
        <section class="relative isolate flex min-h-[min(92svh,58rem)] items-end overflow-hidden bg-navy-950 sm:items-center">
            <x-site.img :path="$hero->image" alt="" eager class="absolute inset-0 -z-20 size-full object-cover" />
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-navy-950 via-navy-950/70 to-navy-950/30 sm:bg-gradient-to-r sm:from-navy-950/95 sm:via-navy-950/70 sm:to-navy-950/10"></div>

            <div class="mx-auto w-full max-w-7xl px-4 pt-32 pb-16 sm:px-6 sm:py-32 lg:px-8">
                <div class="max-w-2xl">
                    @if ($hero->get('eyebrow'))
                        <p class="inline-flex items-center gap-3 rounded-full border border-gold-300/40 bg-white/5 px-4 py-1.5 text-xs font-semibold tracking-[0.2em] text-gold-200 uppercase backdrop-blur">
                            <span class="size-1.5 rounded-full bg-gold-400"></span>{{ $hero->get('eyebrow') }}
                        </p>
                    @endif
                    <h1 class="mt-6 font-serif text-5xl leading-[1.05] font-semibold text-balance text-white sm:text-6xl lg:text-7xl">{{ $hero->title }}</h1>
                    @if ($hero->subtitle)
                        <p class="mt-6 max-w-xl text-lg leading-relaxed text-navy-100 sm:text-xl">{{ $hero->subtitle }}</p>
                    @endif
                    <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                        @if ($hero->get('primary_label'))
                            <x-site.button :href="safe_url($hero->get('primary_url'))" icon="arrow-right">{{ $hero->get('primary_label') }}</x-site.button>
                        @endif
                        @if ($hero->get('secondary_label'))
                            <x-site.button :href="safe_url($hero->get('secondary_url'))" variant="outline-light">{{ $hero->get('secondary_label') }}</x-site.button>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 2. Introduction --}}
    @if ($intro)
        <section class="bg-ivory py-20 sm:py-28">
            <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:gap-20 lg:px-8">
                <div class="relative" data-reveal>
                    <div class="aspect-[4/5] overflow-hidden rounded-3xl shadow-2xl shadow-navy-900/20 sm:aspect-[5/4] lg:aspect-[4/5]">
                        <x-site.img :path="$intro->image" alt="" sizes="(min-width: 1024px) 50vw, 100vw" class="size-full object-cover" />
                    </div>
                    @if ($intro->get('highlight_value'))
                        <div class="absolute -right-2 -bottom-8 max-w-[15rem] rounded-2xl bg-navy-900 p-6 text-white shadow-xl sm:-right-6">
                            <p class="font-serif text-5xl font-semibold text-gold-300">{{ $intro->get('highlight_value') }}<span class="text-3xl">+</span></p>
                            <p class="mt-2 text-sm leading-snug text-navy-100">{{ $intro->get('highlight_label') }}</p>
                        </div>
                    @endif
                </div>
                <div>
                    <x-site.section-heading align="left" :eyebrow="$intro->subtitle" :title="$intro->title" />
                    <x-site.prose-text :text="$intro->get('body')" class="mt-6 text-lg leading-relaxed text-slate-600" data-reveal />
                    @if ($intro->get('cta_label'))
                        <div class="mt-8" data-reveal>
                            <x-site.button :href="safe_url($intro->get('cta_url'))" variant="navy" icon="arrow-right">{{ $intro->get('cta_label') }}</x-site.button>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- 3. Mission & vision --}}
    @if ($mv)
        <section class="relative isolate overflow-hidden bg-navy-950 py-20 sm:py-28">
            @if ($mv->image)
                <x-site.img :path="$mv->image" alt="" class="absolute inset-0 -z-20 size-full object-cover opacity-15" />
            @endif
            <div class="pointer-events-none absolute -top-40 -left-40 -z-10 size-[32rem] rounded-full border border-gold-400/10"></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading dark eyebrow="Why we exist" :title="$mv->title" />
                <div class="mt-14 grid gap-6 md:grid-cols-2">
                    @foreach ([['vision', 'eye', 'Our vision'], ['mission', 'target', 'Our mission']] as [$key, $icon, $label])
                        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-8 backdrop-blur-sm sm:p-10" data-reveal>
                            <span class="grid size-12 place-items-center rounded-xl bg-gold-400 text-navy-950">{{ svg('lucide-'.$icon, ['class' => 'size-6']) }}</span>
                            <p class="mt-6 text-xs font-semibold tracking-[0.2em] text-gold-300 uppercase">{{ $label }}</p>
                            <h3 class="mt-3 font-serif text-2xl font-semibold text-white sm:text-3xl">{{ $mv->get($key.'_title') }}</h3>
                            <p class="mt-4 leading-relaxed text-navy-100">{{ $mv->get($key.'_text') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 4. Areas of intervention --}}
    @if ($focus)
        <section class="py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading :eyebrow="$focus->get('eyebrow')" :title="$focus->title" :subtitle="$focus->subtitle" />
                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($focus->items() as $i => $item)
                        <div class="group relative rounded-3xl border border-slate-200 bg-white p-8 transition duration-300 hover:-translate-y-1 hover:border-gold-300 hover:shadow-xl" data-reveal>
                            <span class="absolute top-6 right-8 font-serif text-6xl font-semibold text-slate-100 transition group-hover:text-gold-100">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="relative grid size-14 place-items-center rounded-2xl bg-navy-900 text-gold-300">
                                {{ svg('lucide-'.($item['icon'] ?? 'star'), ['class' => 'size-7']) }}
                            </span>
                            <h3 class="relative mt-6 font-serif text-2xl font-semibold text-navy-900">{{ $item['title'] ?? '' }}</h3>
                            <p class="relative mt-3 leading-relaxed text-slate-600">{{ $item['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 5. Featured programmes --}}
    @if ($programmeSection && $programmes->isNotEmpty())
        <section class="bg-ivory py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <x-site.section-heading align="left" :eyebrow="$programmeSection->get('eyebrow')" :title="$programmeSection->title" :subtitle="$programmeSection->subtitle" />
                    <x-site.button :href="route('programmes.index')" variant="outline" icon="arrow-right" class="shrink-0">All programmes</x-site.button>
                </div>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($programmes as $programme)
                        <x-site.programme-card :programme="$programme" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 6. Impact statistics --}}
    @if ($stats && count($stats->items()))
        <section class="bg-navy-900 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                @if ($stats->title)
                    <h2 class="sr-only">{{ $stats->title }}</h2>
                @endif
                <dl @class([
                    'grid gap-10 text-center sm:grid-cols-2',
                    'lg:grid-cols-3' => count($stats->items()) === 3,
                    'lg:grid-cols-4' => count($stats->items()) >= 4,
                ])>
                    @foreach ($stats->items() as $item)
                        <div class="flex flex-col-reverse justify-end" data-reveal>
                            <dt class="mx-auto mt-3 max-w-[16rem] text-sm leading-snug text-navy-200">{{ $item['label'] ?? '' }}</dt>
                            <dd class="font-serif text-5xl font-semibold text-gold-300 sm:text-6xl">{{ $item['value'] ?? '' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>
    @endif

    {{-- Belief quote --}}
    @if ($quote && $quote->get('quote'))
        <section class="py-20 sm:py-24">
            <figure class="mx-auto max-w-4xl px-4 text-center sm:px-6" data-reveal>
                <x-lucide-quote class="mx-auto size-10 text-gold-400" />
                <blockquote class="mt-6 font-serif text-3xl leading-snug font-medium text-balance text-navy-900 sm:text-5xl">“{{ $quote->get('quote') }}”</blockquote>
                @if ($quote->get('attribution'))
                    <figcaption class="mt-6 text-sm font-semibold tracking-[0.2em] text-gold-700 uppercase">{{ $quote->get('attribution') }}</figcaption>
                @endif
            </figure>
        </section>
    @endif

    {{-- 7 & 8. Recent activities/events and featured posts --}}
    @if ($eventsSection && $events->isNotEmpty())
        <section class="bg-ivory py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading :eyebrow="$eventsSection->get('eyebrow')" :title="$eventsSection->title" :subtitle="$eventsSection->subtitle" />
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($events as $post)
                        <x-site.post-card :post="$post" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($news)
        <section class="py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <x-site.section-heading align="left" :eyebrow="$news->get('eyebrow')" :title="$news->title" :subtitle="$news->subtitle" />
                    @if ($featuredPosts->isNotEmpty())
                        <x-site.button :href="route('blog.index')" variant="outline" icon="arrow-right" class="shrink-0">All stories</x-site.button>
                    @endif
                </div>
                @if ($featuredPosts->isNotEmpty())
                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($featuredPosts as $post)
                            <x-site.post-card :post="$post" />
                        @endforeach
                    </div>
                @else
                    <x-site.empty-state class="mt-12" icon="newspaper" title="Stories are on their way" text="We will soon be sharing news and stories from our work." />
                @endif
            </div>
        </section>
    @endif

    {{-- 9. Gallery preview --}}
    @if ($gallery && $galleryImages->isNotEmpty())
        <section class="bg-navy-950 py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <x-site.section-heading dark align="left" :eyebrow="$gallery->get('eyebrow')" :title="$gallery->title" :subtitle="$gallery->subtitle" />
                    <x-site.button :href="route('gallery.index')" variant="outline-light" icon="arrow-right" class="shrink-0">View gallery</x-site.button>
                </div>
                <div class="mt-12 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
                    @foreach ($galleryImages as $i => $image)
                        <a href="{{ route('gallery.index') }}" @class(['group relative overflow-hidden rounded-2xl', 'md:col-span-2 md:row-span-2' => $i === 0]) data-reveal>
                            <img src="{{ $image->url($i === 0 ? 'lg' : 'md') }}" srcset="{{ $image->srcset() }}" sizes="(min-width: 768px) 25vw, 50vw"
                                 alt="{{ $image->alt ?: $image->caption }}" loading="lazy" decoding="async"
                                 class="aspect-square size-full object-cover transition duration-700 group-hover:scale-105">
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 10. Get involved --}}
    @if ($involve)
        <section class="relative isolate overflow-hidden py-20 sm:py-28">
            <x-site.img :path="$involve->image" alt="" class="absolute inset-0 -z-20 size-full object-cover" />
            <div class="absolute inset-0 -z-10 bg-navy-950/85"></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading dark :eyebrow="$involve->get('eyebrow')" :title="$involve->title" :subtitle="$involve->subtitle" />
                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($involve->items() as $item)
                        <a href="{{ safe_url($item['url'] ?? '/get-involved') }}" class="group rounded-3xl border border-white/15 bg-white/[0.06] p-8 backdrop-blur transition hover:-translate-y-1 hover:border-gold-400/60 hover:bg-white/10" data-reveal>
                            <span class="grid size-12 place-items-center rounded-xl bg-gold-400 text-navy-950">{{ svg('lucide-'.($item['icon'] ?? 'heart'), ['class' => 'size-6']) }}</span>
                            <h3 class="mt-6 font-serif text-2xl font-semibold text-white">{{ $item['title'] ?? '' }}</h3>
                            <p class="mt-3 leading-relaxed text-navy-100">{{ $item['text'] ?? '' }}</p>
                            <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-gold-300">Learn more <x-lucide-arrow-right class="size-4 transition group-hover:translate-x-1" /></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 11. Donation CTA (only when enabled) --}}
    <x-site.donation-cta />

    {{-- 12. Newsletter --}}
    <x-site.newsletter />
</x-layouts.site>
