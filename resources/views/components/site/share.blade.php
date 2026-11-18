{{-- Social sharing links (no third-party scripts). --}}
@props(['url', 'title'])

@php
    $u = urlencode($url);
    $t = urlencode($title);
    $links = [
        'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$u}",
        'x' => "https://twitter.com/intent/tweet?url={$u}&text={$t}",
        'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$u}",
        'whatsapp' => "https://wa.me/?text={$t}%20{$u}",
    ];
@endphp

<div {{ $attributes->class('rounded-3xl border border-slate-200 p-6') }} x-data="{ copied: false }">
    <p class="text-sm font-semibold text-navy-900">Share</p>
    <div class="mt-4 flex flex-wrap gap-2">
        @foreach ($links as $network => $href)
            <a href="{{ $href }}" target="_blank" rel="noopener" aria-label="Share on {{ $network === 'x' ? 'X' : ucfirst($network) }}"
               class="grid size-10 place-items-center rounded-full bg-slate-100 text-navy-800 transition hover:bg-navy-900 hover:text-gold-300">
                @if ($network === 'whatsapp')
                    <x-lucide-message-circle class="size-4" />
                @else
                    <x-site.social-icon :network="$network" class="size-4" />
                @endif
            </a>
        @endforeach
        <button type="button" aria-label="Copy link"
                @click="navigator.clipboard?.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                class="grid size-10 place-items-center rounded-full bg-slate-100 text-navy-800 transition hover:bg-navy-900 hover:text-gold-300">
            <x-lucide-link x-show="!copied" class="size-4" />
            <x-lucide-check x-cloak x-show="copied" class="size-4" />
        </button>
    </div>
</div>
