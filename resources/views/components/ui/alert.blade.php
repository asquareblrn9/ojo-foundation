@props(['type' => 'info'])

<div role="status" {{ $attributes->class([
    'flex items-start gap-3 rounded-xl border p-4 text-sm',
    'border-sky-200 bg-sky-50 text-sky-800' => $type === 'info',
    'border-emerald-200 bg-emerald-50 text-emerald-800' => $type === 'success',
    'border-amber-200 bg-amber-50 text-amber-800' => $type === 'warning',
    'border-rose-200 bg-rose-50 text-rose-800' => $type === 'error',
]) }}>
    @switch($type)
        @case('success') <x-lucide-circle-check class="mt-px size-4 shrink-0" /> @break
        @case('warning') <x-lucide-triangle-alert class="mt-px size-4 shrink-0" /> @break
        @case('error') <x-lucide-circle-alert class="mt-px size-4 shrink-0" /> @break
        @default <x-lucide-info class="mt-px size-4 shrink-0" />
    @endswitch
    <div>{{ $slot }}</div>
</div>
