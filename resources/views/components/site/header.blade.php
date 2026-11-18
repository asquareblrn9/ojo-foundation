@props(['transparent' => false])

@php
    $links = [
        ['label' => 'Home', 'route' => 'home', 'active' => 'home'],
        ['label' => 'About', 'route' => 'about', 'active' => 'about'],
        ['label' => 'Programmes', 'route' => 'programmes.index', 'active' => 'programmes.*'],
        ['label' => 'News', 'route' => 'blog.index', 'active' => 'blog.*'],
        ['label' => 'Gallery', 'route' => 'gallery.index', 'active' => 'gallery.*'],
        ['label' => 'Contact', 'route' => 'contact', 'active' => 'contact'],
    ];
    $donationsOn = setting('donations_enabled') && (setting('donation_url') || setting('donation_bank_details'));
@endphp

<header x-data="{ open: false, scrolled: false }"
        x-init="scrolled = window.scrollY > 24"
        @scroll.window.passive="scrolled = window.scrollY > 24"
        @keydown.escape.window="open = false"
        :class="(scrolled || open || !{{ $transparent ? 'true' : 'false' }}) ? 'bg-white/95 shadow-sm backdrop-blur' : 'bg-transparent'"
        @class([
            'inset-x-0 top-0 z-50 transition-colors duration-300',
            'fixed' => $transparent,
            'sticky bg-white/95 shadow-sm backdrop-blur' => ! $transparent,
        ])>
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ setting('site_name') }} — home">
            @if ($transparent)
                <x-ui.logo variant="light" x-show="!(scrolled || open)" />
                <x-ui.logo x-cloak x-show="scrolled || open" />
            @else
                <x-ui.logo />
            @endif
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Main">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['active']); @endphp
                <a href="{{ route($link['route']) }}" @if ($active) aria-current="page" @endif
                   @if ($transparent)
                       :class="(scrolled || open) ? '{{ $active ? 'text-navy-900' : 'text-slate-600 hover:text-navy-900' }}' : '{{ $active ? 'text-white' : 'text-white/80 hover:text-white' }}'"
                   @endif
                   @class([
                       'relative rounded-md px-3 py-2 text-sm font-medium transition',
                       'text-navy-900' => ! $transparent && $active,
                       'text-slate-600 hover:text-navy-900' => ! $transparent && ! $active,
                       "after:absolute after:inset-x-3 after:-bottom-0.5 after:h-0.5 after:rounded-full after:bg-gold-500 after:content-['']" => $active,
                   ])>{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ route('get-involved') }}"
               class="rounded-full bg-gold-500 px-5 py-2.5 text-sm font-semibold text-navy-950 shadow-sm transition hover:bg-gold-400">
                {{ $donationsOn ? 'Donate' : 'Get Involved' }}
            </a>
        </div>

        <button type="button" @click="open = !open" class="rounded-md p-2 lg:hidden"
                :class="(scrolled || open || !{{ $transparent ? 'true' : 'false' }}) ? 'text-navy-900' : 'text-white'"
                :aria-expanded="open" aria-controls="mobile-menu" aria-label="Toggle menu">
            <x-lucide-menu x-show="!open" class="size-6" />
            <x-lucide-x x-cloak x-show="open" class="size-6" />
        </button>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" x-cloak x-show="open" x-transition.origin.top class="border-t border-slate-100 bg-white lg:hidden">
        <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 sm:px-6" aria-label="Mobile">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['active']); @endphp
                <a href="{{ route($link['route']) }}" @class([
                    'block rounded-lg px-4 py-3 text-base font-medium',
                    'bg-navy-50 text-navy-900' => $active,
                    'text-slate-700 hover:bg-slate-50' => ! $active,
                ])>{{ $link['label'] }}</a>
            @endforeach
            <a href="{{ route('get-involved') }}" class="mt-3 block rounded-full bg-gold-500 px-5 py-3 text-center font-semibold text-navy-950">
                {{ $donationsOn ? 'Donate' : 'Get Involved' }}
            </a>
        </nav>
    </div>
</header>
