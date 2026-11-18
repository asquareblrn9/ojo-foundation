@php
    $hero = $sections->get('hero');
    $why = $sections->get('why');
    $ways = $sections->get('ways');
    $sponsor = $sections->get('sponsor');
    $volunteer = $sections->get('volunteer');
    $partner = $sections->get('partner');
@endphp

<x-layouts.site :seo="$seo" transparent>
    <x-site.page-hero :title="$hero?->title ?? $page->title" :subtitle="$hero?->subtitle" :eyebrow="$hero?->get('eyebrow')" :image="$hero?->image"
        :breadcrumbs="['Get involved' => null]">
        <div class="mt-10 flex flex-col gap-3 sm:flex-row">
            <x-site.button href="#volunteer" icon="arrow-down">Volunteer</x-site.button>
            <x-site.button href="#partner" variant="outline-light">Partner with us</x-site.button>
        </div>
    </x-site.page-hero>

    @if ($why)
        <section class="py-20 sm:py-28">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:gap-20 lg:px-8">
                <div>
                    <x-site.section-heading align="left" :eyebrow="$why->get('eyebrow')" :title="$why->title" />
                    <x-site.prose-text :text="$why->get('body')" class="mt-6 text-lg leading-relaxed text-slate-600 [&>p:last-child]:font-serif [&>p:last-child]:text-xl [&>p:last-child]:text-navy-900" data-reveal />
                </div>
                @if ($why->image)
                    <div class="aspect-[4/5] overflow-hidden rounded-3xl shadow-xl sm:aspect-[4/3] lg:aspect-[4/5]" data-reveal>
                        <x-site.img :path="$why->image" alt="" sizes="(min-width: 1024px) 50vw, 100vw" class="size-full object-cover" />
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($ways)
        <section class="bg-ivory py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading :eyebrow="$ways->get('eyebrow')" :title="$ways->title" :subtitle="$ways->subtitle" />
                <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($ways->items() as $item)
                        <a href="#{{ $item['anchor'] ?? 'volunteer' }}" class="group rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/70 transition hover:-translate-y-1 hover:shadow-xl" data-reveal>
                            <span class="grid size-12 place-items-center rounded-xl bg-navy-900 text-gold-300 transition group-hover:bg-gold-400 group-hover:text-navy-950">{{ svg('lucide-'.($item['icon'] ?? 'heart'), ['class' => 'size-6']) }}</span>
                            <h3 class="mt-5 font-serif text-xl font-semibold text-navy-900">{{ $item['title'] ?? '' }}</h3>
                            <p class="mt-2 leading-relaxed text-slate-600">{{ $item['text'] ?? '' }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($sponsor)
        <section id="sponsor" class="scroll-mt-24 py-20 sm:py-24">
            <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
                <x-site.section-heading :eyebrow="$sponsor->get('eyebrow')" :title="$sponsor->title" />
                <x-site.prose-text :text="$sponsor->get('body')" class="mt-6 text-lg leading-relaxed text-slate-600" data-reveal />
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row" data-reveal>
                    <x-site.button href="#partner" variant="navy" icon="arrow-right">Register your interest</x-site.button>
                </div>
            </div>
        </section>
    @endif

    <x-site.donation-cta />

    @if ($volunteer)
        <section id="volunteer" class="scroll-mt-24 bg-navy-950 py-20 sm:py-28">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-5 lg:gap-16 lg:px-8">
                <div class="lg:col-span-2">
                    <x-site.section-heading dark align="left" :eyebrow="$volunteer->get('eyebrow')" :title="$volunteer->title" :subtitle="$volunteer->subtitle" />
                    @if (count($volunteer->items()))
                        <ul class="mt-10 space-y-5" data-reveal>
                            @foreach ($volunteer->items() as $item)
                                <li class="flex gap-4">
                                    <x-lucide-circle-check class="mt-0.5 size-5 shrink-0 text-gold-400" />
                                    <div>
                                        <p class="font-semibold text-white">{{ $item['title'] ?? '' }}</p>
                                        <p class="mt-1 text-sm text-navy-200">{{ $item['text'] ?? '' }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="rounded-[2rem] bg-white p-6 shadow-2xl sm:p-10 lg:col-span-3">
                    <livewire:public.volunteer-form :areas="$programmes->all()" />
                </div>
            </div>
        </section>
    @endif

    @if ($partner)
        <section id="partner" class="scroll-mt-24 py-20 sm:py-28">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-5 lg:gap-16 lg:px-8">
                <div class="lg:col-span-2">
                    <x-site.section-heading align="left" :eyebrow="$partner->get('eyebrow')" :title="$partner->title" :subtitle="$partner->subtitle" />
                    <div class="mt-10 rounded-3xl bg-ivory p-8" data-reveal>
                        <p class="font-serif text-xl leading-snug text-navy-900">“We cannot educate every disadvantaged child overnight. But together, we can ensure that more children are given the opportunity to learn, grow, and succeed.”</p>
                    </div>
                </div>
                <div class="rounded-[2rem] bg-white p-6 shadow-xl ring-1 ring-slate-200/70 sm:p-10 lg:col-span-3">
                    <livewire:public.partnership-form />
                </div>
            </div>
        </section>
    @endif

    <x-site.newsletter />
</x-layouts.site>
