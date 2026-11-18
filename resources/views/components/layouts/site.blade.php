{{-- Public site layout. `transparent` overlays the header on a full-bleed hero. --}}
@props(['seo' => null, 'transparent' => false, 'preload' => null])

@php
    $seo ??= \App\Support\Seo::make();
    $ogImage = $seo->imageUrl();
    $gaId = setting('google_analytics_id');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $seo->pageTitle() }}</title>
    <meta name="description" content="{{ $seo->metaDescription() }}">
    @if (setting('seo_keywords'))
        <meta name="keywords" content="{{ setting('seo_keywords') }}">
    @endif
    <link rel="canonical" href="{{ $seo->canonicalUrl() }}">
    @if ($seo->noindex)
        <meta name="robots" content="noindex, nofollow">
    @endif

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ setting('site_name') }}">
    <meta property="og:type" content="{{ $seo->type }}">
    <meta property="og:title" content="{{ $seo->pageTitle() }}">
    <meta property="og:description" content="{{ $seo->metaDescription() }}">
    <meta property="og:url" content="{{ $seo->canonicalUrl() }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    @if ($seo->publishedAt)
        <meta property="article:published_time" content="{{ $seo->publishedAt }}">
    @endif
    @if ($seo->modifiedAt)
        <meta property="article:modified_time" content="{{ $seo->modifiedAt }}">
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seo->pageTitle() }}">
    <meta name="twitter:description" content="{{ $seo->metaDescription() }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <x-ui.favicons />

    @if ($preload)
        <link rel="preload" as="image" href="{{ $preload['src'] }}" @if (! empty($preload['srcset'])) imagesrcset="{{ $preload['srcset'] }}" imagesizes="100vw" @endif fetchpriority="high">
    @endif

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script type="application/ld+json">{!! json_encode(\App\Support\Seo::organizationSchema(), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @foreach ($seo->schema as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endforeach

    @if ($gaId && app()->isProduction())
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($gaId) }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @js($gaId), { anonymize_ip: true });
        </script>
    @endif
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main" class="sr-only z-[100] rounded-md bg-gold-400 px-4 py-2 font-semibold text-navy-950 focus:not-sr-only focus:fixed focus:top-4 focus:left-4">Skip to content</a>

    <x-site.header :transparent="$transparent" />

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <x-site.footer />

    {{-- Always load Livewire (which bundles Alpine): the header menu and share buttons use Alpine. --}}
    @livewireScripts
    @stack('scripts')
</body>
</html>
