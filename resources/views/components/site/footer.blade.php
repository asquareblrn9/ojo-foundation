@php
    $settings = app(\App\Services\SettingsService::class);
    $socials = $settings->socialLinks();
@endphp

<footer class="relative overflow-hidden bg-navy-950 text-navy-200">
    <div class="pointer-events-none absolute -top-32 -right-32 size-96 rounded-full border border-gold-400/10"></div>
    <div class="relative mx-auto max-w-7xl px-4 pt-16 pb-8 sm:px-6 lg:px-8">
        <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-ui.logo variant="light" />
                <p class="mt-2 text-sm font-medium tracking-wide text-gold-300">{{ setting('tagline') }}</p>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">{{ setting('footer_about') }}</p>

                @if ($socials)
                    <div class="mt-6 flex gap-2">
                        @foreach ($socials as $network => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"
                               class="grid size-10 place-items-center rounded-full border border-white/10 text-navy-200 transition hover:border-gold-400 hover:text-gold-300">
                                <x-site.social-icon :network="$network" class="size-4" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:col-span-2">
                <h2 class="font-sans text-sm font-semibold tracking-wider text-white uppercase">Explore</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-gold-300">About us</a></li>
                    <li><a href="{{ route('programmes.index') }}" class="hover:text-gold-300">Programmes</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-gold-300">News &amp; stories</a></li>
                    <li><a href="{{ route('gallery.index') }}" class="hover:text-gold-300">Gallery</a></li>
                    <li><a href="{{ route('get-involved') }}" class="hover:text-gold-300">Get involved</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-gold-300">Contact</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="font-sans text-sm font-semibold tracking-wider text-white uppercase">What we do</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    @forelse ($footerProgrammes as $programme)
                        <li><a href="{{ route('programmes.show', $programme['slug']) }}" class="hover:text-gold-300">{{ $programme['title'] }}</a></li>
                    @empty
                        <li><a href="{{ route('programmes.index') }}" class="hover:text-gold-300">Our programmes</a></li>
                    @endforelse
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="font-sans text-sm font-semibold tracking-wider text-white uppercase">Contact</h2>
                <ul class="mt-5 space-y-4 text-sm">
                    @if (setting('address'))
                        <li class="flex gap-3"><x-lucide-map-pin class="mt-0.5 size-4 shrink-0 text-gold-400" /><span class="whitespace-pre-line">{{ setting('address') }}</span></li>
                    @endif
                    @if (setting('contact_email'))
                        <li class="flex gap-3"><x-lucide-mail class="mt-0.5 size-4 shrink-0 text-gold-400" /><a href="mailto:{{ setting('contact_email') }}" class="break-all hover:text-gold-300">{{ setting('contact_email') }}</a></li>
                    @endif
                    @if (setting('contact_phone'))
                        <li class="flex gap-3"><x-lucide-phone class="mt-0.5 size-4 shrink-0 text-gold-400" /><a href="tel:{{ preg_replace('/[^0-9+]/', '', setting('contact_phone')) }}" class="hover:text-gold-300">{{ setting('contact_phone') }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-8 text-xs text-navy-300 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ setting('footer_copyright') }}</p>
            <div class="flex gap-6">
                <a href="{{ route('privacy') }}" class="hover:text-gold-300">Privacy policy</a>
                <a href="{{ route('terms') }}" class="hover:text-gold-300">Terms &amp; conditions</a>
            </div>
        </div>
    </div>
</footer>
