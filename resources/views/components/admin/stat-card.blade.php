@props(['label', 'value', 'icon' => 'circle', 'href' => null])

<div {{ $attributes->class('card relative overflow-hidden p-5') }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 font-serif text-3xl font-semibold text-navy-900">{{ number_format($value) }}</p>
        </div>
        <span class="grid size-11 place-items-center rounded-xl bg-navy-50 text-navy-700">
            {{ svg('lucide-'.$icon, ['class' => 'size-5']) }}
        </span>
    </div>
    @if ($href)
        <a href="{{ $href }}" wire:navigate class="absolute inset-0" aria-label="View {{ strtolower($label) }}"></a>
    @endif
    <span class="absolute inset-x-0 bottom-0 h-0.5 bg-gradient-to-r from-gold-400/0 via-gold-400/60 to-gold-400/0"></span>
</div>
