<div x-data="lightbox" @keydown.window="onKey($event)">

    @if ($this->categories->isNotEmpty())
        <div class="-mx-4 mb-8 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0 [&::-webkit-scrollbar]:hidden" role="group" aria-label="Filter by category">
            <button type="button" wire:click="filter('')" @class([
                'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition',
                'bg-navy-900 text-white' => $category === '',
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:ring-navy-300' => $category !== '',
            ]) aria-pressed="{{ $category === '' ? 'true' : 'false' }}">All</button>
            @foreach ($this->categories as $cat)
                <button type="button" wire:click="filter('{{ $cat->slug }}')" wire:key="gcat-{{ $cat->id }}" @class([
                    'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition',
                    'bg-navy-900 text-white' => $category === $cat->slug,
                    'bg-white text-slate-700 ring-1 ring-slate-200 hover:ring-navy-300' => $category !== $cat->slug,
                ]) aria-pressed="{{ $category === $cat->slug ? 'true' : 'false' }}">{{ $cat->name }}</button>
            @endforeach
        </div>
    @endif

    @if ($images->isEmpty())
        <x-site.empty-state icon="images" title="Photos coming soon" text="We will be sharing photographs from our work and activities here." />
    @else
        <div class="columns-2 gap-3 sm:gap-4 md:columns-3 lg:columns-4" wire:loading.class="opacity-60" wire:target="filter">
            @foreach ($images as $i => $image)
                <figure class="group relative mb-3 break-inside-avoid overflow-hidden rounded-2xl bg-slate-100 sm:mb-4" wire:key="img-{{ $image->id }}">
                    <button type="button" @click="open({{ $i }})" class="block w-full cursor-zoom-in" aria-label="View larger: {{ $image->caption ?: $image->alt ?: 'photo '.($i + 1) }}"
                            data-lightbox
                            data-src="{{ $image->url('lg') }}"
                            data-srcset="{{ $image->srcset() }}"
                            data-alt="{{ $image->alt ?: ($image->caption ?: 'Gallery photo') }}"
                            data-caption="{{ $image->caption }}"
                            data-description="{{ $image->description }}"
                            data-album="{{ $image->album?->title }}">
                        <img src="{{ $image->url('md') }}" srcset="{{ $image->srcset() }}" sizes="(min-width: 1024px) 25vw, (min-width: 768px) 33vw, 50vw"
                             alt="{{ $image->alt ?: $image->caption }}" loading="lazy" decoding="async"
                             @if ($image->width && $image->height) width="{{ $image->width }}" height="{{ $image->height }}" @endif
                             class="h-auto w-full transition duration-500 group-hover:scale-105">
                    </button>
                    @if ($image->caption)
                        <figcaption class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950/85 to-transparent p-3 pt-10 text-sm text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
                            {{ $image->caption }}
                        </figcaption>
                    @endif
                </figure>
            @endforeach
        </div>

        @if ($hasMore)
            <div class="mt-10 text-center">
                <button type="button" wire:click="loadMore" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-full bg-navy-900 px-7 py-3.5 text-sm font-semibold text-white transition hover:bg-navy-800 disabled:opacity-60">
                    <x-lucide-loader-circle wire:loading wire:target="loadMore" class="size-4 animate-spin" />
                    Load more photos
                </button>
            </div>
        @endif
    @endif

    {{-- Lightbox --}}
    <template x-teleport="body">
        <div x-cloak x-show="index !== null" x-transition.opacity
             class="fixed inset-0 z-[100] flex flex-col bg-navy-950/95 backdrop-blur"
             role="dialog" aria-modal="true" aria-label="Photo viewer" x-trap.noscroll="index !== null">
            <div class="flex items-center justify-between p-4 text-sm text-navy-200">
                <span x-text="index !== null ? `${index + 1} / ${items.length}` : ''"></span>
                <button type="button" @click="close()" class="grid size-11 place-items-center rounded-full text-white hover:bg-white/10" aria-label="Close">
                    <x-lucide-x class="size-6" />
                </button>
            </div>
            <div class="relative flex flex-1 items-center justify-center px-4 sm:px-20" @click.self="close()">
                <button type="button" @click="prev()" x-show="items.length > 1" class="absolute left-2 grid size-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20 sm:left-6" aria-label="Previous photo">
                    <x-lucide-chevron-left class="size-6" />
                </button>
                <template x-if="current">
                    <img :src="current.src" :srcset="current.srcset" sizes="100vw" :alt="current.alt" class="max-h-[75vh] max-w-full rounded-lg object-contain shadow-2xl">
                </template>
                <button type="button" @click="next()" x-show="items.length > 1" class="absolute right-2 grid size-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20 sm:right-6" aria-label="Next photo">
                    <x-lucide-chevron-right class="size-6" />
                </button>
            </div>
            <div class="mx-auto max-w-3xl px-6 pt-4 pb-8 text-center" x-show="current && (current.caption || current.description)">
                <p class="text-xs font-semibold tracking-wide text-gold-300 uppercase" x-show="current?.album" x-text="current?.album"></p>
                <p class="mt-1 font-serif text-xl text-white" x-text="current?.caption"></p>
                <p class="mt-2 text-sm text-navy-200" x-text="current?.description"></p>
            </div>
        </div>
    </template>
</div>
