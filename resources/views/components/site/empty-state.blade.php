@props(['icon' => 'inbox', 'title', 'text' => null])

<div {{ $attributes->class('rounded-2xl border border-dashed border-slate-300 bg-white/60 px-6 py-14 text-center') }}>
    <span class="mx-auto grid size-14 place-items-center rounded-full bg-navy-50 text-navy-600">
        {{ svg('lucide-'.$icon, ['class' => 'size-6']) }}
    </span>
    <h3 class="mt-4 font-serif text-xl text-navy-900">{{ $title }}</h3>
    @if ($text)
        <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">{{ $text }}</p>
    @endif
    {{ $slot }}
</div>
