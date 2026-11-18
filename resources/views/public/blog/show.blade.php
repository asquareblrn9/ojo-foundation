<x-layouts.site :seo="$seo">
    @if ($isPreview)
        <div class="bg-amber-100 px-4 py-3 text-center text-sm font-medium text-amber-900">
            Preview — this article is {{ $post->isScheduled() ? 'scheduled for '.$post->published_at->format('j M Y, g:ia') : 'a draft' }} and is not visible to the public.
        </div>
    @endif

    <article>
        <header class="bg-ivory pt-12 pb-10 sm:pt-16">
            <div class="mx-auto max-w-3xl px-4 sm:px-6">
                <nav aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                        <li><a href="{{ route('home') }}" class="hover:text-navy-900">Home</a></li>
                        <li class="flex items-center gap-2"><x-lucide-chevron-right class="size-3.5" /><a href="{{ route('blog.index') }}" class="hover:text-navy-900">News</a></li>
                        @if ($post->category)
                            <li class="flex items-center gap-2"><x-lucide-chevron-right class="size-3.5" /><a href="{{ route('blog.category', $post->category) }}" class="hover:text-navy-900">{{ $post->category->name }}</a></li>
                        @endif
                    </ol>
                </nav>

                <h1 class="mt-6 font-serif text-4xl leading-tight font-semibold text-balance text-navy-900 sm:text-5xl">{{ $post->title }}</h1>
                @if ($post->excerpt)
                    <p class="mt-5 text-xl leading-relaxed text-slate-600">{{ $post->excerpt }}</p>
                @endif

                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-500">
                    @if ($post->author)
                        <span class="flex items-center gap-3">
                            <x-admin.avatar :user="$post->author" class="size-10 text-xs" />
                            <span>
                                <span class="block font-semibold text-navy-900">{{ $post->author->name }}</span>
                                @if ($post->author->job_title)<span class="block text-xs">{{ $post->author->job_title }}</span>@endif
                            </span>
                        </span>
                    @endif
                    @if ($post->published_at)
                        <span class="flex items-center gap-1.5"><x-lucide-calendar class="size-4" /><time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('j F Y') }}</time></span>
                    @endif
                    <span class="flex items-center gap-1.5"><x-lucide-clock class="size-4" />{{ $post->readingTime() }} min read</span>
                </div>
            </div>
        </header>

        @if ($post->featured_image)
            <div class="mx-auto -mb-2 max-w-5xl px-4 sm:px-6">
                <x-site.img :path="$post->featured_image" :alt="$post->featured_image_alt ?: $post->title" eager sizes="(min-width: 1024px) 1024px, 100vw"
                    class="aspect-[16/9] w-full rounded-3xl object-cover shadow-xl" />
            </div>
        @endif

        <div class="mx-auto grid max-w-5xl gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[1fr_14rem]">
            <div class="min-w-0">
                {{-- Post body is sanitised with HTMLPurifier when saved. --}}
                <div class="prose-foundation">{!! $post->body !!}</div>

                @if ($post->tags->isNotEmpty())
                    <div class="mt-10 flex flex-wrap items-center gap-2 border-t border-slate-200 pt-8">
                        <span class="mr-1 text-sm font-semibold text-navy-900">Tags:</span>
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('blog.tag', $tag) }}" class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700 hover:bg-navy-900 hover:text-white">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="lg:pt-2">
                <div class="lg:sticky lg:top-28">
                    <x-site.share :url="route('blog.show', $post)" :title="$post->title" />
                </div>
            </aside>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="bg-ivory py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading eyebrow="Keep reading" title="Related articles" />
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-site.post-card :post="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.newsletter class="!bg-white" />
</x-layouts.site>
