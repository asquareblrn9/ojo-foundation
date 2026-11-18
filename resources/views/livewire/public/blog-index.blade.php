<div>
    {{-- Search & filters --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0 [&::-webkit-scrollbar]:hidden" role="group" aria-label="Filter by category">
            <button type="button" wire:click="filterCategory('')" @class([
                'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition',
                'bg-navy-900 text-white' => $category === '',
                'bg-white text-slate-700 ring-1 ring-slate-200 hover:ring-navy-300' => $category !== '',
            ]) aria-pressed="{{ $category === '' ? 'true' : 'false' }}">All</button>
            @foreach ($this->categories as $cat)
                <button type="button" wire:click="filterCategory('{{ $cat->slug }}')" wire:key="cat-{{ $cat->id }}" @class([
                    'shrink-0 rounded-full px-4 py-2 text-sm font-medium transition',
                    'bg-navy-900 text-white' => $category === $cat->slug,
                    'bg-white text-slate-700 ring-1 ring-slate-200 hover:ring-navy-300' => $category !== $cat->slug,
                ]) aria-pressed="{{ $category === $cat->slug ? 'true' : 'false' }}">
                    {{ $cat->name }} <span class="opacity-60">({{ $cat->posts_count }})</span>
                </button>
            @endforeach
        </div>

        <div class="relative w-full lg:max-w-xs">
            <label for="blog-search" class="sr-only">Search articles</label>
            <x-lucide-search class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
            <input id="blog-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Search articles…" class="site-input rounded-full pl-11">
        </div>
    </div>

    <div wire:loading.class="opacity-50" wire:target="search,filterCategory,clearFilters,gotoPage,nextPage,previousPage" class="transition-opacity">
        {{-- Featured article --}}
        @if ($featured = $this->featured)
            <article class="group relative mt-10 grid overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200/70 lg:grid-cols-5">
                <div class="relative aspect-[16/10] overflow-hidden lg:col-span-3 lg:aspect-auto lg:min-h-[24rem]">
                    <x-site.img :path="$featured->featured_image" :alt="$featured->featured_image_alt ?: $featured->title" sizes="(min-width: 1024px) 60vw, 100vw"
                        class="absolute inset-0 size-full object-cover transition duration-700 group-hover:scale-105" />
                    <span class="absolute top-5 left-5 rounded-full bg-gold-400 px-3 py-1 text-xs font-bold tracking-wide text-navy-950 uppercase">Featured</span>
                </div>
                <div class="flex flex-col justify-center p-8 sm:p-10 lg:col-span-2">
                    <p class="text-xs font-semibold tracking-wide text-gold-700 uppercase">
                        {{ $featured->category?->name }} · <time datetime="{{ $featured->published_at->toDateString() }}">{{ $featured->published_at->format('j F Y') }}</time>
                    </p>
                    <h2 class="mt-3 font-serif text-3xl leading-tight font-semibold text-navy-900">
                        <a href="{{ route('blog.show', $featured) }}" class="after:absolute after:inset-0">{{ $featured->title }}</a>
                    </h2>
                    <p class="mt-4 leading-relaxed text-slate-600">{{ $featured->summary(220) }}</p>
                    <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700 group-hover:text-gold-600">Read story <x-lucide-arrow-right class="size-4" /></span>
                </div>
            </article>
        @endif

        @if ($posts->isNotEmpty())
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-site.post-card :post="$post" wire:key="post-{{ $post->id }}" />
                @endforeach
            </div>

            <div class="mt-12">{{ $posts->links(data: ['scrollTo' => '#main']) }}</div>
        @elseif (! $featured)
            <x-site.empty-state class="mt-10" icon="newspaper"
                :title="$search !== '' || $category !== '' ? 'No articles match your search' : 'No articles yet'"
                :text="$search !== '' || $category !== '' ? 'Try a different search term or category.' : 'News and stories will appear here soon.'">
                @if ($search !== '' || $category !== '')
                    <button type="button" wire:click="clearFilters" class="mt-6 rounded-full bg-navy-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-800">Clear filters</button>
                @endif
            </x-site.empty-state>
        @endif
    </div>
</div>
