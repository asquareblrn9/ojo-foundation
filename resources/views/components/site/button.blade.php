@props(['href', 'variant' => 'gold', 'icon' => null])

<a href="{{ $href }}" {{ $attributes->class([
    'inline-flex items-center justify-center gap-2 rounded-full px-7 py-3.5 text-sm font-semibold transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2',
    'bg-gold-500 text-navy-950 shadow-lg shadow-gold-900/20 hover:bg-gold-400 focus-visible:outline-gold-400' => $variant === 'gold',
    'bg-navy-900 text-white shadow-lg shadow-navy-900/20 hover:bg-navy-800' => $variant === 'navy',
    'border border-white/40 text-white backdrop-blur-sm hover:border-white hover:bg-white/10' => $variant === 'outline-light',
    'border border-navy-900/20 text-navy-900 hover:border-navy-900 hover:bg-navy-900 hover:text-white' => $variant === 'outline',
]) }}>
    {{ $slot }}
    @if ($icon)
        {{ svg('lucide-'.$icon, ['class' => 'size-4']) }}
    @endif
</a>
