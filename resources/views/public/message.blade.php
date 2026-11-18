<x-layouts.site :seo="$seo">
    <section class="grid min-h-[60vh] place-items-center bg-ivory px-4 py-24">
        <div class="max-w-lg text-center">
            <span class="mx-auto grid size-16 place-items-center rounded-full bg-navy-900 text-gold-300"><x-lucide-mail-check class="size-7" /></span>
            <h1 class="mt-6 font-serif text-4xl font-semibold text-navy-900">{{ $title }}</h1>
            <p class="mt-4 text-lg text-slate-600">{{ $text }}</p>
            <div class="mt-8"><x-site.button :href="route('home')" variant="navy">Back to home</x-site.button></div>
        </div>
    </section>
</x-layouts.site>
