<div>
    <x-admin.page-header :title="$title" :description="$description">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route($exportRoute, array_filter(['status' => $status]))"><x-lucide-download class="size-4" /> Export CSV</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 md:flex-row md:items-center md:justify-between">
            <div class="flex gap-1 overflow-x-auto">
                <button type="button" wire:click="$set('status', '')" @class(['filter-chip', 'bg-navy-900 text-white' => $status === '', 'text-slate-600 hover:bg-slate-100' => $status !== ''])>
                    All <span class="opacity-60">{{ $counts->sum() }}</span>
                </button>
                @foreach ($statuses as $s)
                    <button type="button" wire:click="$set('status', '{{ $s->value }}')" @class(['filter-chip', 'bg-navy-900 text-white' => $status === $s->value, 'text-slate-600 hover:bg-slate-100' => $status !== $s->value])>
                        {{ $s->label() }} <span class="opacity-60">{{ $counts[$s->value] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>
            <div class="relative md:w-72">
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search…" class="form-input pl-9" aria-label="Search">
            </div>
        </div>

        <div class="overflow-x-auto" wire:loading.class="opacity-60">
            <table class="admin-table">
                <thead>
                    <tr><th>Name</th><th class="hidden md:table-cell">Interest</th><th>Status</th><th class="hidden sm:table-cell">Received</th><th class="text-right">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr wire:key="rec-{{ $record->id }}">
                            <td>
                                <a href="{{ route($showRoute, $record) }}" wire:navigate class="font-medium text-navy-900 hover:text-gold-700">{{ $record->{$nameField} }}</a>
                                <p class="text-xs text-slate-500">{{ $record->email }}</p>
                            </td>
                            <td class="hidden md:table-cell">{{ $record->{$detailField} ?: '—' }}</td>
                            <td>
                                <select wire:change="setStatus({{ $record->id }}, $event.target.value)" aria-label="Status" class="rounded-lg border-slate-300 py-1 pr-8 text-xs">
                                    @foreach ($statuses as $s)
                                        <option value="{{ $s->value }}" @selected($record->status === $s)>{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="hidden whitespace-nowrap text-slate-500 sm:table-cell" title="{{ $record->created_at->toDayDateTimeString() }}">{{ $record->created_at->format('j M Y') }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route($showRoute, $record) }}" wire:navigate class="icon-btn" title="View"><x-lucide-eye class="size-4" /></a>
                                    <button type="button" wire:click="delete({{ $record->id }})" wire:confirm="Delete this submission?" class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-16 text-center"><x-lucide-inbox class="mx-auto size-8 text-slate-300" /><p class="mt-2 text-sm text-slate-500">Nothing here yet.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $records->links() }}</div>
        @endif
    </div>
</div>
