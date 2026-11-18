{{-- Banner for inner pages. --}}
@props(['title', 'subtitle' => null, 'eyebrow' => null, 'image' => null, 'breadcrumbs' => []])

<section class="relative isolate overflow-hidden bg-navy-950">
    @if ($image)
        <x-site.img :path="$image" alt="" eager class="absolute inset-0 -z-20 size-full object-cover opacity-40" />
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-navy-950 via-navy-950/85 to-navy-900/40"></div>
    <div class="pointer-events-none absolute -right-20 -bottom-40 -z-10 size-[28rem] rounded-full border border-gold-400/15"></div>

    <div class="mx-auto max-w-7xl px-4 pt-36 pb-20 sm:px-6 sm:pt-40 sm:pb-24 lg:px-8">
        @if ($breadcrumbs)
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-navy-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                    @foreach ($breadcrumbs as $label => $url)
                        <li class="flex items-center gap-2">
                            <x-lucide-chevron-right class="size-3.5 text-navy-400" />
                            @if ($url)
                                <a href="{{ $url }}" class="hover:text-white">{{ $label }}</a>
                            @else
                                <span aria-current="page" class="text-white">{{ $label }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        @if ($eyebrow)
            <p class="text-xs font-semibold tracking-[0.22em] text-gold-300 uppercase">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-3 max-w-4xl font-serif text-4xl leading-tight font-semibold text-balance text-white sm:text-5xl lg:text-6xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-6 max-w-2xl text-lg leading-relaxed text-navy-100">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
