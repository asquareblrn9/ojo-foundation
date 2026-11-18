@props(['eyebrow' => null, 'title' => null, 'subtitle' => null, 'align' => 'center', 'dark' => false])

<div {{ $attributes->class([
    'max-w-3xl',
    'mx-auto text-center' => $align === 'center',
]) }} data-reveal>
    @if ($eyebrow)
        <p @class([
            'inline-flex items-center gap-2 text-xs font-semibold tracking-[0.22em] uppercase',
            'text-gold-600' => ! $dark,
            'text-gold-300' => $dark,
        ])>
            <span class="h-px w-6 bg-current"></span>{{ $eyebrow }}
        </p>
    @endif
    @if ($title)
        <h2 @class([
            'mt-3 font-serif text-3xl leading-tight font-semibold text-balance sm:text-4xl lg:text-[2.75rem]',
            'text-navy-900' => ! $dark,
            'text-white' => $dark,
        ])>{{ $title }}</h2>
    @endif
    @if ($subtitle)
        <p @class([
            'mt-4 text-lg leading-relaxed text-pretty',
            'text-slate-600' => ! $dark,
            'text-navy-200' => $dark,
        ])>{{ $subtitle }}</p>
    @endif
</div>
