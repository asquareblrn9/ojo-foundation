<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ setting('site_name') }} Admin</title>
    <x-ui.favicons />
    @fonts
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="h-full bg-navy-950 font-sans text-slate-700 antialiased">
    <div class="grid min-h-full lg:grid-cols-2">
        <div class="relative hidden overflow-hidden lg:block">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_left,var(--color-navy-700),var(--color-navy-950)_70%)]"></div>
            <div class="absolute -right-24 -bottom-24 size-96 rounded-full border border-gold-400/20"></div>
            <div class="absolute -right-8 -bottom-8 size-64 rounded-full border border-gold-400/30"></div>
            <div class="relative flex h-full flex-col justify-between p-12">
                <x-ui.logo variant="light" />
                <div class="max-w-md">
                    @if ($emblem = app(\App\Services\SettingsService::class)->url('logo'))
                        <img src="{{ $emblem }}" alt="" class="mb-10 size-28 rounded-full shadow-2xl ring-4 ring-gold-400/20" width="112" height="112">
                    @endif
                    <p class="text-xs font-semibold tracking-[0.25em] text-gold-300 uppercase">Content management</p>
                    <h1 class="mt-4 font-serif text-4xl leading-tight text-white">Share the stories that change lives.</h1>
                    <p class="mt-4 text-navy-200">Manage programmes, articles, galleries and enquiries for {{ setting('site_name') }}.</p>
                </div>
                <p class="text-sm text-navy-300">&copy; {{ date('Y') }} {{ setting('site_name') }}</p>
            </div>
        </div>

        <main class="flex items-center justify-center bg-ivory px-4 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <x-ui.logo />
                </div>
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
