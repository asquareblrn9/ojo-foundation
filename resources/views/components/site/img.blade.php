{{--
    Responsive, lazy-loaded image for paths stored by ImageService::store().
    Pass `eager` for above-the-fold images (e.g. the hero).
--}}
@props(['path', 'alt' => '', 'sizes' => '100vw', 'eager' => false, 'width' => null, 'height' => null])

@if ($path)
    <img src="{{ \App\Support\ResponsiveImage::variant($path, 'md') }}"
         @if ($srcset = \App\Support\ResponsiveImage::srcset($path)) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
         alt="{{ $alt }}"
         @if ($width) width="{{ $width }}" @endif
         @if ($height) height="{{ $height }}" @endif
         loading="{{ $eager ? 'eager' : 'lazy' }}"
         @if ($eager) fetchpriority="high" @endif
         decoding="async"
         {{ $attributes }}>
@else
    {{-- Branded fallback when no image has been uploaded. --}}
    <div {{ $attributes->class('bg-[radial-gradient(circle_at_30%_20%,var(--color-navy-600),var(--color-navy-950)_75%)]') }} role="img" aria-label="{{ $alt }}"></div>
@endif
