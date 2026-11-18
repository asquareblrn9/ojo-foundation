@php
    $hero = $sections->get('hero');
    $form = $sections->get('form');
    $settings = app(\App\Services\SettingsService::class);
    $socials = $settings->socialLinks();
    $map = setting('map_embed_url');
    $mapIsSafe = $map && preg_match('#^https://(www\.)?google\.[a-z.]+/maps/embed#i', $map);
@endphp

<x-layouts.site :seo="$seo" transparent>
    <x-site.page-hero :title="$hero?->title ?? $page->title" :subtitle="$hero?->subtitle" :eyebrow="$hero?->get('eyebrow')" :image="$hero?->image"
        :breadcrumbs="['Contact' => null]" />

    <section class="bg-ivory py-20 sm:py-24">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
            {{-- Contact details --}}
            <div class="space-y-4">
                @if (setting('address'))
                    <div class="flex gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70" data-reveal>
                        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-navy-900 text-gold-300"><x-lucide-map-pin class="size-5" /></span>
                        <div>
                            <h2 class="font-sans text-sm font-semibold text-navy-900">Address</h2>
                            <p class="mt-1 whitespace-pre-line text-slate-600">{{ setting('address') }}</p>
                        </div>
                    </div>
                @endif
                @if (setting('contact_email'))
                    <div class="flex gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70" data-reveal>
                        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-navy-900 text-gold-300"><x-lucide-mail class="size-5" /></span>
                        <div class="min-w-0">
                            <h2 class="font-sans text-sm font-semibold text-navy-900">Email</h2>
                            <a href="mailto:{{ setting('contact_email') }}" class="mt-1 block break-all text-slate-600 hover:text-navy-900">{{ setting('contact_email') }}</a>
                        </div>
                    </div>
                @endif
                @if (setting('contact_phone'))
                    <div class="flex gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70" data-reveal>
                        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-navy-900 text-gold-300"><x-lucide-phone class="size-5" /></span>
                        <div>
                            <h2 class="font-sans text-sm font-semibold text-navy-900">Phone</h2>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone')) }}" class="mt-1 block text-slate-600 hover:text-navy-900">{{ setting('contact_phone') }}</a>
                            @if (setting('contact_phone_alt'))
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone_alt')) }}" class="block text-slate-600 hover:text-navy-900">{{ setting('contact_phone_alt') }}</a>
                            @endif
                        </div>
                    </div>
                @endif
                @if (setting('office_hours'))
                    <div class="flex gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70" data-reveal>
                        <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-navy-900 text-gold-300"><x-lucide-clock class="size-5" /></span>
                        <div>
                            <h2 class="font-sans text-sm font-semibold text-navy-900">Office hours</h2>
                            <p class="mt-1 text-slate-600">{{ setting('office_hours') }}</p>
                        </div>
                    </div>
                @endif
                @if ($socials)
                    <div class="rounded-3xl bg-navy-900 p-6" data-reveal>
                        <h2 class="font-sans text-sm font-semibold text-white">Follow us</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($socials as $network => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}" class="grid size-10 place-items-center rounded-full bg-white/10 text-white hover:bg-gold-400 hover:text-navy-950">
                                    <x-site.social-icon :network="$network" class="size-4" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Form --}}
            <div class="rounded-[2rem] bg-white p-6 shadow-xl ring-1 ring-slate-200/70 sm:p-10 lg:col-span-2">
                <h2 class="font-serif text-3xl font-semibold text-navy-900">{{ $form?->title ?? 'Send us a message' }}</h2>
                @if ($form?->subtitle)
                    <p class="mt-2 text-slate-600">{{ $form->subtitle }}</p>
                @endif
                <div class="mt-8">
                    <livewire:public.contact-form />
                </div>
            </div>
        </div>
    </section>

    @if ($mapIsSafe)
        <section aria-label="Map">
            <iframe src="{{ $map }}" class="block h-[420px] w-full border-0 grayscale-[30%]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map showing our location" allowfullscreen></iframe>
        </section>
    @endif
</x-layouts.site>
