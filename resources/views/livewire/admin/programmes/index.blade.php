<div>
    <x-admin.page-header title="Programmes" description="Your areas of intervention. Drag to change the order they appear on the website.">
        <x-slot:actions>
            <x-ui.button :href="route('admin.programmes.create')" wire:navigate><x-lucide-plus class="size-4" /> New programme</x-ui.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($programmes->isEmpty())
        <div class="card px-6 py-16 text-center">
            <x-lucide-hand-heart class="mx-auto size-8 text-slate-300" />
            <p class="mt-2 text-sm text-slate-500">No programmes yet.</p>
        </div>
    @else
        <ul wire:sort="reorder" class="space-y-3">
            @foreach ($programmes as $programme)
                <li wire:key="programme-{{ $programme->id }}" wire:sort:item="{{ $programme->id }}" class="card flex items-center gap-4 p-3 pr-4">
                    <button type="button" wire:sort:handle class="cursor-grab px-1 text-slate-300 hover:text-slate-500" aria-label="Drag to reorder"><x-lucide-grip-vertical class="size-5" /></button>
                    <div class="hidden size-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 sm:block">
                        @if ($programme->featured_image)
                            <img src="{{ \App\Support\ResponsiveImage::variant($programme->featured_image, 'sm') }}" alt="" class="size-full object-cover" loading="lazy">
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('admin.programmes.edit', $programme->id) }}" wire:navigate class="font-semibold text-navy-900 hover:text-gold-700">{{ $programme->title }}</a>
                        <p class="line-clamp-1 text-sm text-slate-500">{{ $programme->short_description }}</p>
                    </div>
                    <x-admin.badge :color="$programme->is_published ? 'emerald' : 'amber'" class="hidden sm:inline-flex">{{ $programme->is_published ? 'Published' : 'Draft' }}</x-admin.badge>
                    <div class="flex gap-1">
                        <button type="button" wire:click="togglePublish({{ $programme->id }})" class="icon-btn" title="{{ $programme->is_published ? 'Unpublish' : 'Publish' }}">
                            @if ($programme->is_published) <x-lucide-eye-off class="size-4" /> @else <x-lucide-send class="size-4" /> @endif
                        </button>
                        <a href="{{ route('programmes.show', $programme) }}" target="_blank" class="icon-btn" title="View"><x-lucide-external-link class="size-4" /></a>
                        <a href="{{ route('admin.programmes.edit', $programme->id) }}" wire:navigate class="icon-btn" title="Edit"><x-lucide-pencil class="size-4" /></a>
                        <button type="button" wire:click="delete({{ $programme->id }})" wire:confirm="Delete the “{{ $programme->title }}” programme?" class="icon-btn-danger" title="Delete"><x-lucide-trash-2 class="size-4" /></button>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
