@if (setting('newsletter_enabled'))
    <section id="newsletter" {{ $attributes->class('bg-ivory py-20 sm:py-24') }}>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-[2rem] bg-navy-900 px-6 py-14 sm:px-12 lg:flex lg:items-center lg:justify-between lg:gap-12 lg:px-16">
                <div class="pointer-events-none absolute -top-24 -right-24 size-72 rounded-full border border-gold-400/20"></div>
                <div class="relative max-w-xl" data-reveal>
                    <x-lucide-mail class="size-8 text-gold-300" />
                    <h2 class="mt-4 font-serif text-3xl font-semibold text-white sm:text-4xl">{{ setting('newsletter_heading') }}</h2>
                    <p class="mt-3 text-navy-100">{{ setting('newsletter_text') }}</p>
                </div>
                <div class="relative mt-8 w-full lg:mt-0 lg:max-w-md">
                    <livewire:public.newsletter-form />
                </div>
            </div>
        </div>
    </section>
@endif
