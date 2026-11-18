{{-- Closing call-to-action used across inner pages. --}}
@props([
    'title' => 'Let us give them more than hope. Let us give them an education.',
    'text' => 'Sponsor a child, make a donation or become a partner — together we can ensure more children are given the opportunity to learn, grow and succeed.',
])

<section {{ $attributes->class('relative isolate overflow-hidden bg-navy-900 py-20 sm:py-24') }}>
    <div class="pointer-events-none absolute -top-24 -right-24 -z-10 size-96 rounded-full border border-gold-400/15"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-16 -z-10 size-80 rounded-full border border-gold-400/10"></div>
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8" data-reveal>
        <h2 class="font-serif text-3xl leading-tight font-semibold text-balance text-white sm:text-5xl">{{ $title }}</h2>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-navy-100">{{ $text }}</p>
        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
            <x-site.button :href="route('get-involved')" icon="arrow-right">Get involved</x-site.button>
            <x-site.button :href="route('contact')" variant="outline-light">Contact us</x-site.button>
        </div>
    </div>
</section>
