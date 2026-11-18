<div>
    <x-admin.page-header title="Newsletter subscribers" description="People who signed up for updates. Pending subscribers have not yet confirmed their email address.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.subscribers.export', ['filter' => $filter])"><x-lucide-download class="size-4" /> Export CSV</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 md:flex-row md:items-center md:justify-between">
            <div class="flex gap-1 overflow-x-auto">
                @foreach (['active' => 'Active', 'pending' => 'Pending', 'unsubscribed' => 'Unsubscribed', 'all' => 'All'] as $key => $label)
                    <button type="button" wire:click="$set('filter', '{{ $key }}')" @class(['filter-chip', 'bg-navy-900 text-white' => $filter === $key, 'text-slate-600 hover:bg-slate-100' => $filter !== $key])>
                        {{ $label }} <span class="opacity-60">{{ $counts[$key] }}</span>
                    </button>
                @endforeach
            </div>
            <div class="relative md:w-72">
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search email…" class="form-input pl-9" aria-label="Search subscribers">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead><tr><th>Email</th><th class="hidden sm:table-cell">Status</th><th class="hidden md:table-cell">Subscribed</th><th></th></tr></thead>
                <tbody>
                    @forelse ($subscribers as $subscriber)
                        <tr wire:key="sub-{{ $subscriber->id }}">
                            <td><span class="font-medium text-navy-900">{{ $subscriber->email }}</span>@if ($subscriber->name)<span class="block text-xs text-slate-500">{{ $subscriber->name }}</span>@endif</td>
                            <td class="hidden sm:table-cell">
                                @if ($subscriber->unsubscribed_at) <x-admin.badge color="rose">Unsubscribed</x-admin.badge>
                                @elseif ($subscriber->confirmed_at) <x-admin.badge color="emerald">Active</x-admin.badge>
                                @else <x-admin.badge color="amber">Pending</x-admin.badge> @endif
                            </td>
                            <td class="hidden text-slate-500 md:table-cell">{{ $subscriber->created_at->format('j M Y') }}</td>
                            <td class="text-right"><button type="button" wire:click="delete({{ $subscriber->id }})" wire:confirm="Remove {{ $subscriber->email }}?" class="icon-btn-danger" title="Remove"><x-lucide-trash-2 class="size-4" /></button></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-14 text-center text-sm text-slate-500">No subscribers here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($subscribers->hasPages())<div class="border-t border-slate-100 px-4 py-3">{{ $subscribers->links() }}</div>@endif
    </div>
</div>
