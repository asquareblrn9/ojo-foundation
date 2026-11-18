@props(['color' => 'slate'])

<span {{ $attributes->class([
    'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium whitespace-nowrap ring-1 ring-inset',
    'bg-slate-50 text-slate-700 ring-slate-200' => $color === 'slate',
    'bg-emerald-50 text-emerald-700 ring-emerald-200' => $color === 'emerald',
    'bg-amber-50 text-amber-800 ring-amber-200' => $color === 'amber',
    'bg-sky-50 text-sky-700 ring-sky-200' => $color === 'sky',
    'bg-rose-50 text-rose-700 ring-rose-200' => $color === 'rose',
    'bg-navy-50 text-navy-800 ring-navy-200' => $color === 'navy',
    'bg-gold-50 text-gold-800 ring-gold-200' => $color === 'gold',
]) }}>{{ $slot }}</span>
