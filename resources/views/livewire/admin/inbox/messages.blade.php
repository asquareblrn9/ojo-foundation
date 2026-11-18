<div>
    <x-admin.page-header title="Messages" description="Enquiries sent through the Contact page.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.messages.export')"><x-lucide-download class="size-4" /> Export CSV</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 md:flex-row md:items-center md:justify-between">
            <div class="flex gap-1 overflow-x-auto">
                @foreach (['all' => 'All', 'unread' => 'Unread', 'open' => 'Open', 'resolved' => 'Resolved'] as $key => $label)
                    <button type="button" wire:click="$set('filter', '{{ $key }}')" @class(['filter-chip', 'bg-navy-900 text-white' => $filter === $key, 'text-slate-600 hover:bg-slate-100' => $filter !== $key])>
                        {{ $label }} <span class="opacity-60">{{ $counts[$key] }}</span>
                    </button>
                @endforeach
            </div>
            <div class="relative md:w-72">
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search name, email or subject…" class="form-input pl-9" aria-label="Search messages">
            </div>
        </div>

        <ul class="divide-y divide-slate-100" wire:loading.class="opacity-60">
            @forelse ($messages as $message)
                <li wire:key="msg-{{ $message->id }}" @class(['flex items-start gap-4 px-4 py-4 sm:px-5', 'bg-gold-50/40' => ! $message->read_at])>
                    <span @class(['mt-2 size-2.5 shrink-0 rounded-full', 'bg-gold-500' => ! $message->read_at, 'bg-transparent' => $message->read_at])></span>
                    <a href="{{ route('admin.messages.show', $message) }}" wire:navigate class="min-w-0 flex-1">
                        <p class="flex flex-wrap items-center gap-x-2">
                            <span @class(['text-navy-900', 'font-semibold' => ! $message->read_at, 'font-medium' => $message->read_at])>{{ $message->name }}</span>
                            <span class="text-xs text-slate-500">{{ $message->email }}</span>
                            @if ($message->resolved_at)<x-admin.badge color="emerald">Resolved</x-admin.badge>@endif
                        </p>
                        <p @class(['truncate text-sm', 'font-semibold text-slate-800' => ! $message->read_at, 'text-slate-700' => $message->read_at])>{{ $message->subject }}</p>
                        <p class="truncate text-sm text-slate-500">{{ Str::limit($message->message, 140) }}</p>
                    </a>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        <span class="text-xs whitespace-nowrap text-slate-400" title="{{ $message->created_at->toDayDateTimeString() }}">{{ $message->created_at->diffForHumans() }}</span>
                        <div class="flex gap-1">
                            <button type="button" wire:click="toggleRead({{ $message->id }})" class="icon-btn" title="Mark as {{ $message->read_at ? 'unread' : 'read' }}">
                                @if ($message->read_at) <x-lucide-mail class="size-4" /> @else <x-lucide-mail-open class="size-4" /> @endif
                            </button>
                            <button type="button" wire:click="toggleResolved({{ $message->id }})" class="icon-btn" title="{{ $message->resolved_at ? 'Reopen' : 'Mark resolved' }}"><x-lucide-circle-check @class(['size-4', 'text-emerald-600' => $message->resolved_at]) /></button>
                            <button type="button" wire:click="delete({{ $message->id }})" wire:confirm="Delete this message from {{ $message->name }}?" class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-16 text-center">
                    <x-lucide-inbox class="mx-auto size-8 text-slate-300" />
                    <p class="mt-2 text-sm text-slate-500">No messages {{ $filter !== 'all' || $search ? 'match this filter' : 'yet' }}.</p>
                </li>
            @endforelse
        </ul>

        @if ($messages->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $messages->links() }}</div>
        @endif
    </div>
</div>
