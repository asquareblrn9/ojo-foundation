<x-layouts.site :seo="tap(\App\Support\Seo::make('Page not found'), fn ($s) => $s->noindex = true)">
    <section class="grid min-h-[70vh] place-items-center bg-ivory px-4 py-24">
        <div class="max-w-xl text-center">
            <p class="font-serif text-8xl font-semibold text-gold-500">404</p>
            <h1 class="mt-4 font-serif text-4xl font-semibold text-navy-900">We couldn't find that page</h1>
            <p class="mt-4 text-lg text-slate-600">The page you are looking for may have moved or no longer exists.</p>
            <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
                <x-site.button :href="route('home')" variant="navy">Back to home</x-site.button>
                <x-site.button :href="route('contact')" variant="outline">Contact us</x-site.button>
            </div>
        </div>
    </section>
</x-layouts.site>
