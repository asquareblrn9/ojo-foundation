@php
    $hero = $sections->get('hero');
    $founder = $sections->get('founder');
@endphp

<x-layouts.site :seo="$seo" transparent>
    <x-site.page-hero :title="$hero?->title ?? $page->title" :subtitle="$hero?->subtitle" :eyebrow="$hero?->get('eyebrow')" :image="$hero?->image"
        :breadcrumbs="['About us' => null]" />

    {{-- Section navigation --}}
    <nav class="sticky top-20 z-30 border-b border-slate-200 bg-white/95 backdrop-blur" aria-label="On this page">
        <div class="mx-auto flex max-w-7xl gap-1 overflow-x-auto px-4 py-3 text-sm font-medium whitespace-nowrap sm:px-6 lg:px-8 [&::-webkit-scrollbar]:hidden">
            @foreach (['overview' => 'Overview', 'story' => 'Our story', 'founder' => 'Founder\'s message', 'vision' => 'Vision & mission', 'values' => 'What we believe', 'approach' => 'Our approach', 'team' => 'Leadership'] as $key => $label)
                @if ($sections->has($key))
                    <a href="#{{ $key }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-navy-50 hover:text-navy-900">{{ $label }}</a>
                @endif
            @endforeach
        </div>
    </nav>

    {{-- Overview & story: alternating image/text --}}
    @foreach (['overview', 'story'] as $i => $key)
        @if ($section = $sections->get($key))
            <section id="{{ $key }}" @class(['scroll-mt-36 py-20 sm:py-24', 'bg-ivory' => $i === 1])>
                <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:gap-20 lg:px-8">
                    <div @class(['lg:order-2' => $i === 1]) data-reveal>
                        <div class="aspect-[4/3] overflow-hidden rounded-3xl shadow-xl shadow-navy-900/10">
                            <x-site.img :path="$section->image" alt="" sizes="(min-width: 1024px) 50vw, 100vw" class="size-full object-cover" />
                        </div>
                    </div>
                    <div>
                        <x-site.section-heading align="left" :eyebrow="$section->get('eyebrow')" :title="$section->title" />
                        <x-site.prose-text :text="$section->get('body')" class="mt-6 text-lg leading-relaxed text-slate-600" data-reveal />
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    {{-- Founder's message --}}
    @if ($founder)
        <section id="founder" class="scroll-mt-36 bg-navy-950 py-20 sm:py-28">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading dark :eyebrow="$founder->get('eyebrow')" :title="$founder->title" />
                <article class="relative mt-12 rounded-[2rem] bg-white p-8 shadow-2xl sm:p-14" data-reveal>
                    <x-lucide-quote class="absolute -top-6 left-8 size-12 rounded-full bg-gold-400 p-3 text-navy-950 sm:left-14" />
                    @if ($founder->get('salutation'))
                        <p class="font-serif text-xl text-navy-900 italic">{{ $founder->get('salutation') }}</p>
                    @endif
                    <x-site.prose-text :text="$founder->get('body')" class="mt-6 text-lg leading-relaxed text-slate-700" />
                    <div class="mt-10 flex items-center gap-5 border-t border-slate-100 pt-8">
                        @if ($founder->image)
                            <x-site.img :path="$founder->image" :alt="$founder->get('name')" sizes="80px" class="size-20 rounded-full object-cover" />
                        @else
                            <span class="grid size-16 shrink-0 place-items-center rounded-full bg-navy-900 font-serif text-xl font-semibold text-gold-300">FO</span>
                        @endif
                        <div>
                            @if ($founder->get('signoff'))
                                <p class="text-sm text-slate-500 italic">{{ $founder->get('signoff') }}</p>
                            @endif
                            <p class="mt-1 font-serif text-xl font-semibold text-navy-900">{{ $founder->get('name') }}</p>
                            <p class="text-sm text-slate-600">{{ $founder->get('role') }}</p>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    @endif

    {{-- Vision & mission --}}
    @if ($vision = $sections->get('vision'))
        <section id="vision" class="scroll-mt-36 py-20 sm:py-28">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-12 lg:gap-16 lg:px-8">
                <div class="lg:col-span-5">
                    <div class="lg:sticky lg:top-40">
                        <x-site.section-heading align="left" :eyebrow="$vision->get('eyebrow')" :title="$vision->title" />
                        @if ($vision->image)
                            <div class="mt-8 aspect-[4/3] overflow-hidden rounded-3xl" data-reveal>
                                <x-site.img :path="$vision->image" alt="" sizes="(min-width: 1024px) 40vw, 100vw" class="size-full object-cover" />
                            </div>
                        @endif
                    </div>
                </div>
                <div class="lg:col-span-7">
                    <x-site.prose-text :text="$vision->get('body')" class="text-lg leading-relaxed text-slate-600 [&>p:first-child]:font-serif [&>p:first-child]:text-2xl [&>p:first-child]:leading-snug [&>p:first-child]:text-navy-900 [&>p:last-child]:font-semibold [&>p:last-child]:text-navy-900" data-reveal />

                    @if ($mission = $sections->get('mission'))
                        <div class="mt-12 rounded-3xl bg-navy-900 p-8 sm:p-10" data-reveal>
                            <p class="text-xs font-semibold tracking-[0.2em] text-gold-300 uppercase">{{ $mission->get('eyebrow') }}</p>
                            <h3 class="mt-3 font-serif text-2xl font-semibold text-white sm:text-3xl">{{ $mission->title }}</h3>
                            <x-site.prose-text :text="$mission->get('body')" class="mt-4 leading-relaxed text-navy-100" />
                        </div>
                    @endif

                    @if ($objectives = $sections->get('objectives'))
                        <div class="mt-12" data-reveal>
                            <h3 class="font-serif text-2xl font-semibold text-navy-900">{{ $objectives->title }}</h3>
                            <ol class="mt-6 space-y-5">
                                @foreach ($objectives->items() as $n => $item)
                                    <li class="flex gap-5">
                                        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-gold-100 font-semibold text-gold-800">{{ $n + 1 }}</span>
                                        <div>
                                            <p class="font-semibold text-navy-900">{{ $item['title'] ?? '' }}</p>
                                            <p class="mt-1 text-slate-600">{{ $item['text'] ?? '' }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Core values --}}
    @if ($values = $sections->get('values'))
        <section id="values" class="scroll-mt-36 bg-ivory py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading :eyebrow="$values->get('eyebrow')" :title="$values->title" :subtitle="$values->subtitle" />
                <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($values->items() as $item)
                        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/70" data-reveal>
                            <span class="grid size-12 place-items-center rounded-xl bg-navy-900 text-gold-300">{{ svg('lucide-'.($item['icon'] ?? 'star'), ['class' => 'size-6']) }}</span>
                            <h3 class="mt-5 font-serif text-xl font-semibold text-navy-900">{{ $item['title'] ?? '' }}</h3>
                            <p class="mt-2 leading-relaxed text-slate-600">{{ $item['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Our approach --}}
    @if ($approach = $sections->get('approach'))
        <section id="approach" class="scroll-mt-36 py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading :eyebrow="$approach->get('eyebrow')" :title="$approach->title" :subtitle="$approach->subtitle" />
                <div class="mt-14 grid gap-px overflow-hidden rounded-3xl bg-slate-200 sm:grid-cols-2">
                    @foreach ($approach->items() as $item)
                        <div class="bg-white p-8 sm:p-10" data-reveal>
                            <span class="text-gold-600">{{ svg('lucide-'.($item['icon'] ?? 'star'), ['class' => 'size-8']) }}</span>
                            <h3 class="mt-5 font-serif text-2xl font-semibold text-navy-900">{{ $item['title'] ?? '' }}</h3>
                            <p class="mt-3 leading-relaxed text-slate-600">{{ $item['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Leadership --}}
    @if (($teamSection = $sections->get('team')) && $team->isNotEmpty())
        <section id="team" class="scroll-mt-36 bg-ivory py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading :eyebrow="$teamSection->get('eyebrow')" :title="$teamSection->title" :subtitle="$teamSection->subtitle" />
                <div @class(['mt-14 grid gap-8', 'mx-auto max-w-md' => $team->count() === 1, 'sm:grid-cols-2 lg:grid-cols-3' => $team->count() > 1])>
                    @foreach ($team as $member)
                        <article class="overflow-hidden rounded-3xl bg-white text-center shadow-sm ring-1 ring-slate-200/70" data-reveal>
                            <div class="aspect-square bg-navy-900">
                                @if ($member->photo)
                                    <x-site.img :path="$member->photo" :alt="$member->name" sizes="(min-width: 1024px) 33vw, 100vw" class="size-full object-cover" />
                                @else
                                    <div class="grid size-full place-items-center bg-[radial-gradient(circle_at_30%_20%,var(--color-navy-600),var(--color-navy-950)_75%)]">
                                        <span class="font-serif text-6xl font-semibold text-gold-300">{{ $member->initials() }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-8">
                                <h3 class="font-serif text-2xl font-semibold text-navy-900">{{ $member->name }}</h3>
                                <p class="mt-1 text-sm font-semibold tracking-wide text-gold-700 uppercase">{{ $member->position }}</p>
                                @if ($member->bio)
                                    <p class="mt-4 leading-relaxed text-slate-600">{{ $member->bio }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.cta-band />
</x-layouts.site>
