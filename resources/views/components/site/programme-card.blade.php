@props(['programme'])

<article {{ $attributes->class('group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition duration-300 hover:-translate-y-1 hover:shadow-xl') }} data-reveal>
    <div class="relative aspect-[4/3] overflow-hidden">
        <x-site.img :path="$programme->featured_image" :alt="$programme->title" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
            class="size-full object-cover transition duration-700 group-hover:scale-105" width="800" height="600" />
        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/60 to-transparent"></div>
        @if ($programme->icon)
            <span class="absolute bottom-4 left-4 grid size-12 place-items-center rounded-xl bg-gold-400 text-navy-950 shadow-lg">
                {{ svg('lucide-'.$programme->icon, ['class' => 'size-6']) }}
            </span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-6">
        <h3 class="font-serif text-xl font-semibold text-navy-900">
            <a href="{{ route('programmes.show', $programme) }}" class="after:absolute after:inset-0">{{ $programme->title }}</a>
        </h3>
        <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600">{{ $programme->short_description }}</p>
        <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700 group-hover:text-gold-600">
            Learn more <x-lucide-arrow-right class="size-4 transition group-hover:translate-x-1" />
        </span>
    </div>
</article>
