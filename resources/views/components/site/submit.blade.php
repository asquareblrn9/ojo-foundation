@props(['target' => 'submit'])

<button type="submit" wire:loading.attr="disabled" wire:target="{{ $target }}"
        {{ $attributes->class('inline-flex w-full items-center justify-center gap-2 rounded-full bg-navy-900 px-8 py-4 text-sm font-semibold text-white shadow-lg transition hover:bg-navy-800 disabled:opacity-60 sm:w-auto') }}>
    <x-lucide-loader-circle wire:loading wire:target="{{ $target }}" class="size-4 animate-spin" />
    <x-lucide-send wire:loading.remove wire:target="{{ $target }}" class="size-4" />
    {{ $slot }}
</button>
