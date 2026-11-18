<div class="mx-auto max-w-4xl">
    <a href="{{ route('admin.messages.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-navy-900"><x-lucide-arrow-left class="size-4" /> All messages</a>

    <article class="card mt-4">
        <header class="flex flex-col gap-4 border-b border-slate-100 p-6 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="font-serif text-2xl text-navy-900">{{ $message->subject }}</h2>
                <p class="mt-2 text-sm text-slate-600">
                    From <strong class="text-navy-900">{{ $message->name }}</strong>
                    &lt;<a href="mailto:{{ $message->email }}" class="text-navy-700 hover:underline">{{ $message->email }}</a>&gt;
                    @if ($message->phone) · <a href="tel:{{ preg_replace('/[^0-9+]/', '', $message->phone) }}" class="text-navy-700 hover:underline">{{ $message->phone }}</a>@endif
                </p>
                <p class="mt-1 text-xs text-slate-400">{{ $message->created_at->format('l j F Y, g:ia') }}</p>
            </div>
            @if ($message->resolved_at)
                <x-admin.badge color="emerald">Resolved {{ $message->resolved_at->diffForHumans() }}</x-admin.badge>
            @else
                <x-admin.badge color="amber">Open</x-admin.badge>
            @endif
        </header>

        <div class="p-6 text-base leading-relaxed whitespace-pre-line text-slate-700">{{ $message->message }}</div>

        <footer class="flex flex-wrap gap-2 border-t border-slate-100 p-4">
            <x-ui.button :href="'mailto:'.$message->email.'?subject='.rawurlencode('Re: '.$message->subject)"><x-lucide-mail class="size-4" /> Reply by email</x-ui.button>
            <x-ui.button type="button" variant="secondary" wire:click="toggleResolved">
                <x-lucide-circle-check class="size-4" /> {{ $message->resolved_at ? 'Reopen' : 'Mark resolved' }}
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" wire:click="markUnread">Mark unread</x-ui.button>
            <x-ui.button type="button" variant="ghost" wire:click="delete" wire:confirm="Delete this message?" class="ml-auto text-rose-600 hover:bg-rose-50"><x-lucide-trash-2 class="size-4" /> Delete</x-ui.button>
        </footer>
    </article>
</div>
