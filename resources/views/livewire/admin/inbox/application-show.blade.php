<div class="mx-auto max-w-5xl">
    <a href="{{ route($indexRoute) }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-navy-900"><x-lucide-arrow-left class="size-4" /> Back</a>

    <div class="mt-4 grid gap-6 lg:grid-cols-3">
        <article class="card lg:col-span-2">
            <header class="border-b border-slate-100 p-6">
                <p class="text-xs font-semibold tracking-wide text-gold-700 uppercase">{{ $title }}</p>
                <h2 class="mt-1 font-serif text-2xl text-navy-900">{{ $record->{$nameField} }}</h2>
                <p class="mt-1 text-sm text-slate-500">Received {{ $record->created_at->format('l j F Y, g:ia') }}</p>
            </header>
            <dl class="divide-y divide-slate-100">
                @foreach ($fields as $field => $label)
                    @continue(blank($record->{$field}))
                    <div class="grid gap-1 px-6 py-4 sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-slate-500">{{ $label }}</dt>
                        <dd class="text-sm whitespace-pre-line text-slate-800 sm:col-span-2">
                            @if ($field === 'email')
                                <a href="mailto:{{ $record->email }}" class="text-navy-700 hover:underline">{{ $record->email }}</a>
                            @elseif ($field === 'phone')
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $record->phone) }}" class="text-navy-700 hover:underline">{{ $record->phone }}</a>
                            @elseif ($field === 'website')
                                <a href="{{ safe_url($record->website) }}" target="_blank" rel="noopener nofollow" class="text-navy-700 hover:underline">{{ $record->website }}</a>
                            @else
                                {{ $record->{$field} }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </article>

        <form wire:submit="save" class="card h-fit space-y-4 p-6" novalidate>
            <x-ui.select label="Status" model="status" :options="collect($statuses)->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all()" />
            <x-ui.textarea label="Internal notes" model="internal_notes" rows="6" help="Only visible to administrators." />
            @if ($record->reviewer)
                <p class="text-xs text-slate-500">Last updated by {{ $record->reviewer->name }} {{ $record->reviewed_at?->diffForHumans() }}.</p>
            @endif
            <div class="flex flex-wrap gap-2">
                <x-ui.button target="save">Save</x-ui.button>
                <x-ui.button :href="'mailto:'.$record->email" variant="secondary"><x-lucide-mail class="size-4" /> Email</x-ui.button>
            </div>
            <button type="button" wire:click="delete" wire:confirm="Delete this submission permanently from the list?" class="text-sm font-medium text-rose-600 hover:underline">Delete submission</button>
        </form>
    </div>
</div>
