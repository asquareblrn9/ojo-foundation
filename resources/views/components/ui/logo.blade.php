{{--
    Brand lockup: the circular emblem (Settings → Logo) beside the foundation
    name and tagline, echoing the official logo. Falls back to a monogram
    when no emblem has been uploaded.
--}}
@props(['variant' => 'dark', 'compact' => false, 'tagline' => true, 'size' => 'md'])

@php
    $settings = app(\App\Services\SettingsService::class);
    $emblem = $settings->url('logo');
    $name = setting('site_name', 'Fola Ojo Foundation');
@endphp

<span {{ $attributes->class('inline-flex items-center gap-3') }}>
    @if ($emblem)
        <img src="{{ $emblem }}" alt="{{ $compact ? $name : '' }}" class="{{ $size === 'sm' ? 'size-10' : 'size-11 sm:size-12' }} shrink-0 rounded-full" width="48" height="48">
    @else
        <span @class([
            'grid size-11 shrink-0 place-items-center rounded-full font-serif text-lg font-bold ring-2 sm:size-12',
            'bg-navy-900 text-gold-300 ring-gold-400' => $variant === 'dark',
            'bg-gold-400 text-navy-950 ring-white/30' => $variant === 'light',
        ])>{{ mb_substr($name, 0, 1) }}</span>
    @endif

    @unless ($compact)
        <span class="leading-none">
            <span @class([
                'block font-serif font-bold tracking-wide uppercase',
                'text-[1.05rem] sm:text-lg' => $size === 'md',
                'text-[0.9rem]' => $size === 'sm',
                'text-navy-900' => $variant === 'dark',
                'text-white' => $variant === 'light',
            ])>{{ $name }}</span>
            @if ($tagline && setting('tagline'))
                <span @class([
                    'mt-1 block font-serif text-sm italic',
                    'text-gold-600' => $variant === 'dark',
                    'text-gold-300' => $variant === 'light',
                ])>{{ setting('tagline') }}</span>
            @endif
        </span>
    @endunless
</span>
