{{-- Switch bound to a boolean property. --}}
@props(['label', 'model', 'help' => null, 'live' => false])

<label {{ $attributes->class('flex cursor-pointer items-start justify-between gap-4') }}>
    <span>
        <span class="block text-sm font-medium text-slate-800">{{ $label }}</span>
        @if ($help)<span class="mt-0.5 block text-xs text-slate-500">{{ $help }}</span>@endif
    </span>
    <span class="relative mt-0.5 inline-flex shrink-0">
        <input type="checkbox" @if ($live) wire:model.live="{{ $model }}" @else wire:model="{{ $model }}" @endif class="peer sr-only">
        <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-navy-800 peer-focus-visible:ring-4 peer-focus-visible:ring-navy-500/20"></span>
        <span class="absolute top-0.5 left-0.5 size-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
    </span>
</label>
