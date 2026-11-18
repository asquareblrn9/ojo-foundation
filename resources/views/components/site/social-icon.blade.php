@props(['network'])

@switch($network)
    @case('facebook') <x-lucide-facebook {{ $attributes }} /> @break
    @case('instagram') <x-lucide-instagram {{ $attributes }} /> @break
    @case('linkedin') <x-lucide-linkedin {{ $attributes }} /> @break
    @case('youtube') <x-lucide-youtube {{ $attributes }} /> @break
    @case('x')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        @break
    @case('tiktok')
        <svg {{ $attributes }} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.6 6.7a5.3 5.3 0 0 1-3.2-1.1 5.3 5.3 0 0 1-2-3.6h-3.3v13.1a2.8 2.8 0 1 1-2-2.7V9a6.1 6.1 0 1 0 5.3 6V8.4a8.6 8.6 0 0 0 5.2 1.7z"/></svg>
        @break
    @default <x-lucide-globe {{ $attributes }} />
@endswitch
