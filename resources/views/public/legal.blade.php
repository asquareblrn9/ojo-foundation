<x-layouts.site :seo="$seo">
    <section class="bg-ivory pt-16 pb-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <h1 class="font-serif text-4xl font-semibold text-navy-900 sm:text-5xl">{{ $page->title }}</h1>
            <p class="mt-4 text-sm text-slate-500">Last updated {{ $page->updated_at->format('j F Y') }}</p>
        </div>
    </section>
    <section class="py-14">
        {{-- Page body is sanitised with HTMLPurifier when saved. --}}
        <div class="prose-foundation mx-auto max-w-3xl px-4 sm:px-6">{!! $page->body !!}</div>
    </section>
</x-layouts.site>
