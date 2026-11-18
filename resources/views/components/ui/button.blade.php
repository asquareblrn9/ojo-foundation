{{--
    Button / link. Variants: primary, gold, secondary, danger, ghost.
    Pass `target` (Livewire action name) to show a loading spinner while it runs.
--}}
@props(['variant' => 'primary', 'href' => null, 'type' => 'submit', 'target' => null, 'size' => 'md'])

@php
    $classes = \Illuminate\Support\Arr::toCssClasses([
        'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        'px-3 py-1.5 text-xs' => $size === 'sm',
        'px-4 py-2.5 text-sm' => $size === 'md',
        'px-6 py-3 text-base' => $size === 'lg',
        'bg-navy-900 text-white shadow-sm hover:bg-navy-800 focus-visible:outline-navy-900' => $variant === 'primary',
        'bg-gold-500 text-navy-950 shadow-sm hover:bg-gold-400 focus-visible:outline-gold-500' => $variant === 'gold',
        'border border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-50' => $variant === 'secondary',
        'bg-rose-600 text-white shadow-sm hover:bg-rose-500 focus-visible:outline-rose-600' => $variant === 'danger',
        'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => $variant === 'ghost',
    ]);
@endphp

@if ($href)
    {{-- For links, `target` is the HTML link target (e.g. _blank). --}}
    <a href="{{ $href }}" @if ($target) target="{{ $target }}" @if ($target === '_blank') rel="noopener" @endif @endif {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}
        @if ($target) wire:loading.attr="disabled" wire:target="{{ $target }}" @endif>
        @if ($target)
            <svg wire:loading wire:target="{{ $target }}" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
