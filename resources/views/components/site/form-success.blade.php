@props(['title', 'text', 'action' => null, 'actionLabel' => 'Send another'])

<div class="py-10 text-center" role="status">
    <span class="mx-auto grid size-16 place-items-center rounded-full bg-emerald-100 text-emerald-600">
        <x-lucide-circle-check class="size-8" />
    </span>
    <h3 class="mt-6 font-serif text-2xl font-semibold text-navy-900">{{ $title }}</h3>
    <p class="mx-auto mt-3 max-w-md text-slate-600">{{ $text }}</p>
    @if ($action)
        <button type="button" wire:click="{{ $action }}" class="mt-8 text-sm font-semibold text-navy-700 underline decoration-gold-400 underline-offset-4 hover:text-navy-900">{{ $actionLabel }}</button>
    @endif
</div>
