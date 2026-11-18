@props(['post'])

<article {{ $attributes->class('group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition duration-300 hover:-translate-y-1 hover:shadow-xl') }} data-reveal>
    <div class="relative aspect-[16/10] overflow-hidden">
        <x-site.img :path="$post->featured_image" :alt="$post->featured_image_alt ?: $post->title" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
            class="size-full object-cover transition duration-700 group-hover:scale-105" width="800" height="500" />
        @if ($post->category)
            <span class="absolute top-4 left-4 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-navy-800 shadow-sm">{{ $post->category->name }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <time datetime="{{ $post->published_at?->toDateString() }}" class="text-xs font-medium tracking-wide text-gold-700 uppercase">
            {{ $post->published_at?->format('j F Y') }}
        </time>
        <h3 class="mt-2 font-serif text-xl leading-snug font-semibold text-navy-900">
            <a href="{{ route('blog.show', $post) }}" class="after:absolute after:inset-0">{{ $post->title }}</a>
        </h3>
        <p class="mt-3 line-clamp-3 flex-1 text-sm leading-relaxed text-slate-600">{{ $post->summary() }}</p>
        <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700 group-hover:text-gold-600">
            Read story <x-lucide-arrow-right class="size-4 transition group-hover:translate-x-1" />
        </span>
    </div>
</article>
